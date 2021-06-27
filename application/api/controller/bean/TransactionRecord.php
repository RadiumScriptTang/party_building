<?php
namespace app\api\controller\bean;


class TransactionRecord
{
    private $attempt_id;
    private $is_last_answer_correct;
    private $current_question_id;
    private $current_level = 1;
    private $current_question_answer;
    private $n_rest_questions = 15;
    private $n_current_level_question = 0;
    private $n_current_level_correct = 0;

    public function __construct($attempt_id, $question)
    {
        $this->current_question_id = $question->id;
        $this->attempt_id = $attempt_id;
        $this->current_question_answer = $question->answer;
    }

    public function judge_answer($user_answer)
    {
        $this->is_last_answer_correct = ($user_answer == $this->current_question_answer);
        $this->n_rest_questions--;
        if ($this->is_last_answer_correct)
        {
            $this->n_current_level_correct++;
        }
        $this->n_current_level_question++;

        // 当前等级 答对两题 考虑升级或持平
        if ($this->n_current_level_correct >= 2){
            if ($this->current_level < 5){
                $this->level_change(+1);
            } else {
                $this->level_change(0);
            }
        } elseif ($this->n_current_level_question - $this->n_current_level_correct >= 2){ //当前等级已做3题 考虑降级或者持平
            if ($this->current_level > 1){
                $this->level_change(-1);
            } else {
                $this->level_change(0);
            }
        }
        return $this->is_last_answer_correct;
    }
    public function get_next_question_level()
    {

        // 如果机会用光
        if ($this->n_rest_questions == 0){
            return 0;
        }
        // 如果当前等级不是5
        if ($this->n_rest_questions < 3 && $this->current_level != 5){
            return 0;
        }
        return $this->current_level;
    }

    public function set_question($question)
    {
        $this->current_question_id = $question->id;
        $this->current_question_answer = $question->answer;
    }

    public function get_attempt_id()
    {
        return $this->attempt_id;
    }
    public function get_question_id()
    {
        return $this->current_question_id;
    }
    public function get_current_level()
    {
        return $this->current_level;
    }
    public function get_answer()
    {
        return $this->current_question_answer;
    }
    function level_change($change)
    {
        $this->current_level += $change;
        $this->n_current_level_correct = 0;
        $this->n_current_level_question = 0;
    }


}