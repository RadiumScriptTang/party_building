<?php

namespace app\api\controller;

use app\dal\model\User;
use app\dal\model\Question;
use app\dal\model\Attempt;
use app\dal\model\AttemptDetail;
use think\Controller;
use think\Session;
use app\api\controller\bean\TransactionRecord;


class UserControl extends Controller
{
    public function user_sign_in()
    {
        $inputs = input();
        $wx_id = $inputs["wx_id"];
        $user = User::get([
            "wx_id" => $wx_id
        ]);


        if (!$user || $wx_id == null) {
            $user = User::create([
                "name" => "",
                "tel" => "",
                "wx_id" => $wx_id,
                "email" => "",
                "university" => "",
                "region" => "",
                "enroll_year" => 0
            ]);
            $wx_id = sha1($user->id);
            $user->wx_id = $wx_id;
            if (array_key_exists("from_wx_id", $inputs)) {
                $user->from_wx_id = $inputs["from_wx_id"];
            }
            $user->save();
        }

        // 上Session
        Session::set("user_id", $user->id);
        Session::set("user_name", $user->name);
        Session::set("wx_id", $user->wx_id);
//        Session::set("user_university", $user->university);

        return [
            "status" => 0,
            "msg" => "welcome",
            "data" => $user
        ];
    }

    public function user_logout()
    {
        Session::set("user_id", null);
        Session::set("user_name", null);
        Session::set("wx_id", null);

//        Session::set("user_university", null);
    }

    // 用户点击开始答题时执行
    public function update_user_info()
    {
        $inputs = input();
        $name = $inputs["name"];
        $region = $inputs["region"];
        $tel = $inputs["tel"];
        $university = $inputs["university"];
        $year = $inputs["enroll_year"];
//        $month = $inputs["enroll_month"];

        $user_id = Session::get("user_id");
        $user = User::get($user_id);
        $user->save([
            "name" => $name,
            "region" => $region,
            "tel" => $tel,
            "university" => $university,
            "enroll_year" => $year
        ]);

        Session::set("user_name", $user->name);

        return [
            "status" => 0,
            "msg" => "updated"
        ];
    }

    public function begin_transaction()
    {
        $user_id = Session::get("user_id");
        if (!$user_id) {
            return [
                "status" => 1,
                "msg" => "Please Login"
            ];
        }

        // 创建一个attempt对象
        $attempt = Attempt::create([
            "user_id" => $user_id,
            "is_last" => true
        ]);

        //

        // 选择第一题
        $question = Question::get_random(1);
        $record = new TransactionRecord($attempt->id, $question);
        Session::set("record", $record);

        $question->id = rand(); // 混乱掉Question id避免攻击
        $question->answer = "#"; // 删除答案


        return [
            "status" => 0,
            "msg" => "begin!",
            "data" => $question
        ];
    }

    public function answer_question()
    {
        $user_id = Session::get("user_id");
        if (!$user_id) {
            return [
                "status" => 1,
                "msg" => "Please login"
            ];
        }

        $inputs = input();
        $answer = $inputs["answer"];
        $time_consumed = $inputs["time"];

        // 判断正确与否
        $record = Session::get("record");
        $is_correct = $record->judge_answer($answer);

        // 记录历史信息
        $last_question_id = $record->get_question_id();
        $last_level = $record->get_current_level();
        $last_answer = $record->get_answer();

        // 写回记录
        Session::set("record", $record);

        // 记录本次答题
        $attempt_detail = AttemptDetail::create([
            "attempt_id" => $record->get_attempt_id(),
            "question_id" => $last_question_id,
            "user_answer" => $answer,
            "is_correct" => $is_correct,
            "time_consumed" => $time_consumed,
            "level" => $last_level
        ]);

        return [
            "status" => 0,
            "msg" => "submitted",
            "data" => $last_answer,
            "is_correct" => $is_correct,
            "level" => $record->get_current_level(),
            "user_answer" => $answer

        ];
    }

    public function request_next_question()
    {
        // 准备下次题目
        $record = Session::get("record");
        $next_level = $record->get_next_question_level();
        // 游戏结束
        if ($next_level == 0) {
            // 挑战结束
            $history = $this->summary_history($record->get_attempt_id());

            // 写入数据库
            $attempt_id = $record->get_attempt_id();

            // 之前尝试全部作废
            Attempt::update([
                "is_last" => false
            ], [
                "user_id" => Session::get("user_id")
            ]);

            // 本次记为最后一次，并记录信息
            $attempt = Attempt::get($attempt_id);

            $attempt->is_last = true;
            $attempt->is_complete = true;
            $attempt->final_level = $history["final_level"];
            $attempt->final_acc = $history["final_acc"];
            $attempt->final_time = $history["final_time"];
            $attempt->final_correct = $history["n_question_correct"];
            $attempt->rank = $history["rank"];
            $attempt->save();

            // 生成信息session片段
            $this->set_share_image_info($attempt->final_correct, $attempt->final_level, $attempt->final_correct, $attempt->final_time, $attempt->rank);

            // 答题结束 释放record
            Session::set("record", null);
            return [
                "status" => 2,
                "msg" => "game over",
                "data" => $history
            ];
        }

        $question = Question::get_random($next_level);
        $record->set_question($question);
        $question->id = rand();
        $question->answer = "#";
        Session::set("record", $record);
        return [
            "status" => 0,
            "msg" => "normal",
            "data" => $question,
        ];
    }

    public function test()
    {
        $id = input("id");
        return [
            "status" => 0,
            "data" => $this->summary_history($id)
        ];
    }

    function summary_history($attempt_id)
    {
        $attempt_detail_list = AttemptDetail::all([
            "attempt_id" => $attempt_id
        ]);

        $n_question_by_level = [0, 0, 0, 0, 0];
        $n_correct_question_by_level = [0, 0, 0, 0, 0];
        $total_time_by_level = [0, 0, 0, 0, 0];
        $acc_list = [0, 0, 0, 0, 0];
        $time_consumed_list = [0, 0, 0, 0, 0];
        $total_acc = 0;
        $total_time = 0;
        $total_correct = 0;

        $final_level = 1;

        $question_levels = [];

        foreach ($attempt_detail_list as $detail) {
            $level = $detail->level;
            $is_correct = $detail->is_correct;
            $time_consumed = $detail->time_consumed;
            $n_question_by_level[$level - 1]++;
            if ($is_correct) {
                $n_correct_question_by_level[$level - 1]++;
                $total_time_by_level[$level - 1] += $time_consumed;
                $total_time += $time_consumed;
                $total_correct++;
            }

            $total_acc += $is_correct ? 1 : 0;
            array_push($question_levels, $detail->level);
        }

        $final_level = array_pop($question_levels);

        for ($i = 0; $i < 5; $i++) {

            $acc_list[$i] = $n_question_by_level[$i] == 0 ? "-" : $n_correct_question_by_level[$i] / $n_question_by_level[$i];
            $time_consumed_list[$i] = $n_correct_question_by_level[$i] == 0 ? "-" : $total_time_by_level[$i] / $n_correct_question_by_level[$i];
        }

        $total_acc /= array_sum($n_question_by_level);
        $total_time = $total_correct == 0 ? "-" : $total_time / $total_correct;

        $final_acc = $acc_list[$final_level - 1];
        $final_time = $time_consumed_list[$final_level - 1];

        //定段位
        $all_ranks = ["一心一意", "再接再厉", "三省吾身", "名扬四海", "学富五车", "六韬三略", "七步才华", "才高八斗", "九天揽月", "十年磨剑"];
        $max_level = max($question_levels);
        $rank = "";
        if ($max_level > $final_level) {
            $rank = $all_ranks[2 * ($final_level - 1) + 1];
        } else {
            $rank = $all_ranks[2 * ($final_level - 1)];
        }

        if ($final_level == 5){
            $is_increasing = true;
            for ($i = 0; $i < count($question_levels) - 1; $i++){
               if ($question_levels[$i] > $question_levels[$i + 1]) {
                   $is_increasing = false;
               }
            }
            if ($is_increasing){
                $rank = $all_ranks[9];
            } else {
                $rank = $all_ranks[8];
            }
        }
        return [
            "acc_by_level" => $acc_list,
            "time_by_level" => $time_consumed_list,
            "total_acc" => $total_acc,
            "avg_time" => $total_time,
            "final_level" => $final_level,
            "final_acc" => $final_acc,
            "final_time" => $final_time,
            "n_question" => array_sum($n_question_by_level),
            "n_question_correct" => array_sum($n_correct_question_by_level),
            "n_final_question" => $n_correct_question_by_level[$final_level - 1],
            "rank" => $rank
        ];
    }

    private function set_share_image_info($n_correct, $final_level, $final_correct, $final_time, $rank)
    {
        $info = array();
        $info["name"] = Session::get("user_name");
        $info["n_correct"] = $n_correct;
        $info["rank"] = $rank;
        // 计算超过多少人
        $n_valid_attempt = Attempt::countAllValidAttempt();
        $n_bypass = Attempt::countByPass($final_level, $final_correct, $final_time);

        $by_pass_global = $n_bypass / $n_valid_attempt;

        $n_valid_domestic = Attempt::countValidDomestic();
        $n_bypass_domestic = Attempt::countByPassDomestic($final_level, $final_correct, $final_time);
        $by_pass_domestic = $n_bypass_domestic / $n_valid_domestic;

        $info["bypass_global"] = $by_pass_global * 100;
        $info["bypass_domestic"] = $by_pass_domestic * 100;
        $info["wx_id"] = Session::get("wx_id");
        Session::set("render_info", $info);
    }

    public function join_test($final_level, $final_correct, $final_time)
    {
        return Attempt::countByPassDomestic($final_level, $final_correct, $final_time);
    }

    public function duanwei_test()
    {
        return $this->summary_history(184);
    }
}

