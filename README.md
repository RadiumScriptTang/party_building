建党百年 强国知识5分钟快测
===============
### 一、项目背景
为庆祝中国共产党建党100周年，我们开发了“建党百年——强国知识5分钟快测”，以快问快答的形式评测用户的强国知识储备情况。
本应用在也可以应用于其他问答场景，相比于常见的问答应用，本应用的特色之处在于设计了一种答题等级规则，根据用户的答题情况动态调整用户等级，从而为用户选择难度相当的题目。

本应用属于Web项目，用户方面支持移动端访问答题，管理员方面可由PC端进行访问，包含了常见问答测评应用的基本功能，包括基础的答题功能，用户行为记录功能，计算排名功能，生成答题报告功能，分享功能等。
本项目基于ThinkPHP框架开发，用户可以轻松克隆本项目部署于Xampp服务器上，并个性化定制自己的答题应用。

### 二、基本功能
- 登录功能：用户可以填写自己的信息、地区及单位等信息，首次填写后将在用户端永久保存，再次参与时可以直接登陆。
- 答题功能：用户登录后即开始答题，每道题目显示剩余的答题时间，时间到后自动提交空白答案。
- 用户行为记录：用户在答题过程中的行为会被按照时序记录，包括用户回答的题目、每题开始及结束时间等。
- 计算排名功能：用户答题结束后，系统将根据当前用户的答题等级计算其在所有用户中的排名，等级相同的，答题时间更少的用户将排名更靠前。
- 生成答题报告：用户答题结束后，根据用户的答题情况生成报告图片，包含了用户的答题结果以及排名，并根据素材渲染海报。
- 分享功能：用户答题结束后，可以分享自己的答题报告；其他用户可以通过扫描报告上的二维码参与答题。

### 三、特色功能
本项目的特色功能在于设计了独特的答题评级机制，根据用户的实时答题情况，为用户调整题目难度，确保用户能够渐入佳境，从而更准确地判断用户的水平。

下图展示了该特色设计的工作流。我们设置总测试题数为K，且以M题为一个题目组，规定当用户在一个题目组中答对
N题（N不大于M）后即可提升一个等级；若用户已错题数大于M - N，则等级降低一级。当用户挑战完最高等级的题目组并成功后，即可获得答题报告。此外，若用户答题总数达到预设的K题，也会立即获得答题报告。
![](https://img-blog.csdnimg.cn/fe5d67e61e834a0786e57befc48cb559.png?x-oss-process=image/watermark,type_d3F5LXplbmhlaQ,shadow_50,text_Q1NETiBAUmFkaXVtVGFuZw==)

### 四、工程架构及规模
本应用基于ThinkPHP框架构建，采用了MVC的Web应用设计模式，即M——Model， V——View， C——Controller。M对应了数据库模型，方便对数据库建模并进行链式查询；V为前端视图；C为后端控制器，负责实现各类API。本应用的总代码量约为4970行，其分布如下表：

类型     | 行数 | 语言
-------- | ----- | ----
数据库模型  | 150 | PHP
应用程序接口  | 790 | PHP
页面控制器  | 100 | PHP
页面      | 1400 | HTML/JS
静态资源文件 | 2530 | CSS/JS/TTF

### 五、上线运行情况
本应用于2021年7月1日正式上线运行，运行周期一周。运行期间没有发生重大程序漏洞或服务宕机事件。运行期间总计服务用户646人，总答题参与次数1712次，总计收集数据上万条。未来的研究中，我们将对这些数据进行分析。