<?php
/**
 * A bundler for ThinkPHP
 * User: mr5
 * Date: 14-3-24
 * Time: 下午4:39
 *
 */
namespace gallery\ThinkHaml;
use gallery\ThinkHaml\Autoloader;
Autoloader::register();
use Think;
use ThinkHaml;
use Think\Hook;
class ThinkPHPBundler
{
    // 当前模板文件
    protected   $templateFile    =   '';
    // 模板变量
    public      $tVar            =   array();
    public      $config          =   array();
    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        $this->config['cache_path']         =   C('CACHE_PATH');
        $this->config['template_suffix']    =   C('TMPL_TEMPLATE_SUFFIX');
        $this->config['cache_suffix']       =   C('TMPL_CACHFILE_SUFFIX');
    }
    // 模板变量获取和设置
    public function get($name)
    {
        if(isset($this->tVar[$name]))
            return $this->tVar[$name];
        else
            return false;
    }

    public function set($name,$value)
    {
        $this->tVar[$name]= $value;
    }
    /**
     * @param string $templateFile  模板文件
     * @param array $templateVar    模板变量
     * @param string  $prefix 模板标识前缀
     */
    public function fetch($templateFile,$templateVar,$prefix='') {
//        exit('hello');
        $this->tVar         =   $templateVar;
        $templateCacheFile  =   $this->loadTemplate($templateFile,$prefix);
        Think\Storage::load($templateCacheFile,$this->tVar,null,'tpl');
    }
    /**
     * 加载主模板并缓存
     * @access public
     * @param string $tmplTemplateFile 模板文件
     * @param string $prefix 模板标识前缀
     * @return string
     * @throws ThinkExecption
     */
    public function loadTemplate ($tmplTemplateFile,$prefix='')
    {
//        exit(dump($tmplTemplateFile));
        if(is_file($tmplTemplateFile)) {
            $this->templateFile    =  $tmplTemplateFile;
            // 读取模板文件内容
            $tmplContent =  file_get_contents($tmplTemplateFile);
        }else{
            $tmplContent =  $tmplTemplateFile;
        }
        // 根据模版文件名定位缓存文件
        $tmplCacheFile = $this->config['cache_path'].$prefix.md5($tmplTemplateFile).$this->config['cache_suffix'];
        // 编译模板内容
        $tmplContent =  $this->compiler($tmplContent);
        Think\Storage::put($tmplCacheFile,trim($tmplContent),'tpl');
        return $tmplCacheFile;
    }
    /**
     * 编译模板文件内容
     * @access protected
     * @param mixed $tmplContent 模板内容
     * @return string
     */
    protected function compiler($tmplContent)
    {
        //模板解析
        $tmplContent =  $this->parse($tmplContent);
        // 还原被替换的Literal标签
        $tmplContent =  preg_replace_callback('/<!--###literal(\d+)###-->/is', array($this, 'restoreLiteral'), $tmplContent);
        // 添加安全代码
        $tmplContent =  '<?php if (!defined(\'THINK_PATH\')) exit();?>'.$tmplContent;
        // 优化生成的php代码
        $tmplContent = str_replace('?><?php','',$tmplContent);
        // 模版编译过滤标签
        Hook::listen('template_filter',$tmplContent);
        return strip_whitespace($tmplContent);
    }
    /**
     * 模板解析入口
     * @access public
     * @param string $content 要解析的模板内容
     * @return string
     */
    public function parse($content) {
        $haml = new ThinkHaml\Environment('php');
        //dump(RUNTIME_PATH);
        $content = $haml->compileString($content, $this->templateFile);
        // 检查include语法
        $content    =   $this->parseInclude($content);
        return $content;
    }
    // 解析模板中的include标签
    protected function parseInclude($content, $extend = false) {
        // 解析继承
        if($extend)
            $content    =   $this->parseExtend($content);
        // 解析布局
        // $content    =   $this->parseLayout($content);
        // 读取模板中的include标签
        $find       =   preg_match_all('/( *)\<include\s(.+?)\s*?\>/is',$content,$matches);
        if($find) {
            for($i=0;$i<$find;$i++) {
                $include    =   $matches[2][$i];
                $array      =   $this->parseXmlAttrs($include);
                $file       =   $array['file'];
                unset($array['file']);
                $content    =   str_replace($matches[0][$i],$this->parseIncludeItem($file,$array,$matches[1][$i]),$content);
            }
        }
        return $content;
    }
    /**
     * 加载公共模板并缓存 和当前模板在同一路径，否则使用相对路径
     * @access private
     * @param string $tmplPublicName  公共模板文件名
     * @param array $vars  要传递的变量列表
     * @param string $indent 缩进字符串
     * @return string
     */
    private function parseIncludeItem($tmplPublicName,$vars=array(), $indent=''){
        // 分析模板文件名并读取内容
        $parseStr = $this->parseTemplateName($tmplPublicName);
        $parseStr = $this->parse($parseStr);
        // DEBUG模式下显示引入的文件注释
        APP_DEBUG && $parseStr = "\n<!-- {$tmplPublicName} -->\n".$parseStr;
        // 补齐缩进
        $content_arr = explode("\n",$parseStr);
        foreach($content_arr AS $_k => $_content) {

            $content_arr[$_k] = $indent.$_content;
        }
        $parseStr = implode("\n", $content_arr);
        // DEBUG模式下显示引入的文件注释
        APP_DEBUG && $parseStr = $parseStr."<!-- /{$tmplPublicName} -->\n";
        // 再次对包含文件进行模板分析
        return $this->parseInclude($parseStr);
    }
    /**
     * 分析XML属性
     * @access private
     * @param string $attrs  XML属性字符串
     * @return array
     */
    private function parseXmlAttrs($attrs) {
        $xml        =   '<tpl><tag '.$attrs.' /></tpl>';
        $xml        =   simplexml_load_string($xml);
        if(!$xml)
            E(L('_XML_TAG_ERROR_'));
        $xml        =   (array)($xml->tag->attributes());
        $array      =   array_change_key_case($xml['@attributes']);
        return $array;
    }

    /**
     * 分析加载的模板文件并读取内容 支持多个模板文件读取
     * @param string $templateName 模板文件名
     * @return string
     */
    private function parseTemplateName($templateName)
    {
        if(substr($templateName,0,1)=='$')
            //支持加载变量文件名
            $templateName = $this->get(substr($templateName,1));
        $array  =   explode(',',$templateName);
        $parseStr   =   '';
        foreach ($array as $templateName){
            if(empty($templateName)) continue;
            if(false === strpos($templateName,$this->config['template_suffix'])) {
                // 解析规则为 模块@主题/控制器/操作
                $templateName   =   T($templateName);
            }
            // 获取模板文件内容
            $parseStr .= file_get_contents($templateName);
        }
        return $parseStr;
    }

} 