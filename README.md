# CodeIgniter-ai
CI动态加载类

========

## 安装 ##

1.将文件“autoinstance_helper.php”放到application/helpers目录下面。

2.修改配置文件“application/config/autoload.php”,添加自动载入：
	$autoload['helper'] = array('autoinstance');

3.任何地方即可调用

## 示例 ##


#### ai::CI() ####

    # ai::CI()->config->item('base_url'); //获取配置项

#### ai::config() ####

    # ai::config('redis')['conf']; //获取redis的配置项

#### ai::model() ####

    # ai::model('name')->method(); //载入“models/M_name_model.php”文件并调用“method”方法

#### ai::lib() ####

    # ai::lib('name')->method(); //载入“libraries/MY_name.php”文件并调用“method”方法

#### ai::library() ####

    # ai::library('name')->method(); //同上，载入“libraries/MY_name.php”文件并调用“method”方法

#### ai::db() ####

    # ai::db()->query($sql); //载入默认DB并调用“query”方法执行SQL

    # ai::db('group2')->query($sql); //载入“group2”的配置DB并调用“query”方法执行SQL

#### ai::helper() ####

    # ai::helper('text')->ellipsize('string', 3); //载入text文本辅助函数并调用“ellipsize”方法

