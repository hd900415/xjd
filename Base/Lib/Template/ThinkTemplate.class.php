<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * ThinkPHP����ģ��������
 * ֧��XML��ǩ����ͨ��ǩ��ģ�����
 * ������ģ������ ֧�ֶ�̬����
 * @category   Think
 * @package  Think
 * @subpackage  Template
 * @author liu21st <liu21st@gmail.com>
 */
class ThinkTemplate {

    // ģ��ҳ��������ı�ǩ���б�
    protected $tagLib = array();
    // ��ǰģ���ļ�
    protected $templateFile = '';
    // ģ�����
    public $tVar = array();
    public $config = array();
    private $literal = array();
    private $block = array();

    /**
     * �ܹ�����
     * @access public
     */
    public function __construct() {
        $this->config['cache_path'] = C('CACHE_PATH');
        $this->config['template_suffix'] = C('TMPL_TEMPLATE_SUFFIX');
        $this->config['cache_suffix'] = C('TMPL_CACHFILE_SUFFIX');
        $this->config['tmpl_cache'] = C('TMPL_CACHE_ON');
        $this->config['cache_time'] = C('TMPL_CACHE_TIME');
        $this->config['taglib_begin'] = $this->stripPreg(C('TAGLIB_BEGIN'));
        $this->config['taglib_end'] = $this->stripPreg(C('TAGLIB_END'));
        $this->config['tmpl_begin'] = $this->stripPreg(C('TMPL_L_DELIM'));
        $this->config['tmpl_end'] = $this->stripPreg(C('TMPL_R_DELIM'));
        $this->config['default_tmpl'] = C('TEMPLATE_NAME');
        $this->config['layout_item'] = C('TMPL_LAYOUT_ITEM');
    }

    private function stripPreg($str) {
        return str_replace(
                array('{', '}', '(', ')', '|', '[', ']', '-', '+', '*', '.', '^', '?'), array('\{', '\}', '\(', '\)', '\|', '\[', '\]', '\-', '\+', '\*', '\.', '\^', '\?'), $str);
    }

    // ģ�������ȡ������
    public function get($name) {
        if (isset($this->tVar[$name]))
            return $this->tVar[$name];
        else
            return false;
    }

    public function set($name, $value) {
        $this->tVar[$name] = $value;
    }

    /**
     * ����ģ��
     * @access public
     * @param string $tmplTemplateFile ģ���ļ�
     * @param array  $templateVar ģ�����
     * @param string $prefix ģ���ʶǰ׺
     * @return void
     */
    public function fetch($templateFile, $templateVar, $prefix = '') {
        $this->tVar = $templateVar;
        $templateCacheFile = $this->loadTemplate($templateFile, $prefix);
        // ģ�����б����ֽ��Ϊ��������
        extract($templateVar, EXTR_OVERWRITE);
        //����ģ�滺���ļ�
        include $templateCacheFile;
    }

    /**
     * ������ģ�岢����
     * @access public
     * @param string $tmplTemplateFile ģ���ļ�
     * @param string $prefix ģ���ʶǰ׺
     * @return string
     * @throws ThinkExecption
     */
    public function loadTemplate($tmplTemplateFile, $prefix = '') {
        if (is_file($tmplTemplateFile)) {
            $this->templateFile = $tmplTemplateFile;
            // ��ȡģ���ļ�����
            $tmplContent = file_get_contents($tmplTemplateFile);
        } else {
            $tmplContent = $tmplTemplateFile;
        }
        // ����ģ���ļ�����λ�����ļ�
        $tmplCacheFile = $this->config['cache_path'] . $prefix . md5($tmplTemplateFile) . $this->config['cache_suffix'];

        // �ж��Ƿ����ò���
        if (C('LAYOUT_ON')) {
            if (false !== strpos($tmplContent, '{__NOLAYOUT__}')) { // ���Ե������岻ʹ�ò���
                $tmplContent = str_replace('{__NOLAYOUT__}', '', $tmplContent);
            } else { // �滻���ֵ���������
                $layoutFile = THEME_PATH . C('LAYOUT_NAME') . $this->config['template_suffix'];
                $tmplContent = str_replace($this->config['layout_item'], $tmplContent, file_get_contents($layoutFile));
            }
        }
        // ����ģ������
        $tmplContent = $this->compiler($tmplContent);
        // ���ģ��Ŀ¼
        $dir = dirname($tmplCacheFile);
        if (!is_dir($dir))
            mkdir($dir, 0755, true);
        //��дCache�ļ�
        if (false === file_put_contents($tmplCacheFile, trim($tmplContent)))
            throw_exception(L('_CACHE_WRITE_ERROR_') . ':' . $tmplCacheFile);
        return $tmplCacheFile;
    }

    /**
     * ����ģ���ļ�����
     * @access protected
     * @param mixed $tmplContent ģ������
     * @return string
     */
    protected function compiler($tmplContent) {
        //ģ�����
        $tmplContent = $this->parse($tmplContent);
        // ��ԭ���滻��Literal��ǩ
        //$tmplContent = preg_replace('/<!--###literal(\d+)###-->/eis', "\$this->restoreLiteral('\\1')", $tmplContent);
        $tmplContent = preg_replace_callback('/<!--###literal(\d+)###-->/is', function($r) {
            return $this->restoreLiteral($r[1]);
        }, $tmplContent);
        // ��Ӱ�ȫ����
        $tmplContent = '<?php if (!defined(\'THINK_PATH\')) exit();?>' . $tmplContent;
        if (C('TMPL_STRIP_SPACE')) {
            /* ȥ��html�ո��뻻�� */
            $find = array('~>\s+<~', '~>(\s+\n|\r)~');
            $replace = array('><', '>');
            $tmplContent = preg_replace($find, $replace, $tmplContent);
        }
        // �Ż����ɵ�php����
        $tmplContent = str_replace('?><?php', '', $tmplContent);
        return strip_whitespace($tmplContent);
    }

    /**
     * ģ��������
     * ֧����ͨ��ǩ��TagLib���� ֧���Զ����ǩ��
     * @access public
     * @param string $content Ҫ������ģ������
     * @return string
     */
    public function parse($content) {
        // ����Ϊ�ղ�����
        if (empty($content))
            return '';
        $begin = $this->config['taglib_begin'];
        $end = $this->config['taglib_end'];
        // ���include�﷨
        $content = $this->parseInclude($content);
        // ���PHP�﷨
        $content = $this->parsePhp($content);
        // �����滻literal��ǩ����
        //$content    =   preg_replace('/'.$begin.'literal'.$end.'(.*?)'.$begin.'\/literal'.$end.'/eis',"\$this->parseLiteral('\\1')",$content);
        $content = preg_replace_callback('/' . $begin . 'literal' . $end . '(.*?)' . $begin . '\/literal' . $end . '/is', function($r) {
            return $this->parseLiteral($r[1]);
        }, $content);

        // ��ȡ��Ҫ����ı�ǩ���б�
        // ��ǩ��ֻ��Ҫ����һ�Σ�����������һ��
        // һ������ļ�����ǰ��
        // ��ʽ��<taglib name="html,mytag..." />
        // ��TAGLIB_LOAD����Ϊtrueʱ�Ż���м��
        if (C('TAGLIB_LOAD')) {
            $this->getIncludeTagLib($content);
            if (!empty($this->tagLib)) {
                // �Ե����TagLib���н���
                foreach ($this->tagLib as $tagLibName) {
                    $this->parseTagLib($tagLibName, $content);
                }
            }
        }
        // Ԥ�ȼ��صı�ǩ�� ������ÿ��ģ����ʹ��taglib��ǩ���� ������ʹ�ñ�ǩ��XMLǰ׺
        if (C('TAGLIB_PRE_LOAD')) {
            $tagLibs = explode(',', C('TAGLIB_PRE_LOAD'));
            foreach ($tagLibs as $tag) {
                $this->parseTagLib($tag, $content);
            }
        }
        // ���ñ�ǩ�� ����ʹ��taglib��ǩ����Ϳ���ʹ�� ���Ҳ���ʹ�ñ�ǩ��XMLǰ׺
        $tagLibs = explode(',', C('TAGLIB_BUILD_IN'));
        foreach ($tagLibs as $tag) {
            $this->parseTagLib($tag, $content, true);
        }
        //������ͨģ���ǩ {tagName}
        //$content = preg_replace('/(' . $this->config['tmpl_begin'] . ')([^\d\s' . $this->config['tmpl_begin'] . $this->config['tmpl_end'] . '].+?)(' . $this->config['tmpl_end'] . ')/eis', "\$this->parseTag('\\2')", $content);
        $content = preg_replace_callback('/(' . $this->config['tmpl_begin'] . ')([^\d\s' . $this->config['tmpl_begin'] . $this->config['tmpl_end'] . '].+?)(' . $this->config['tmpl_end'] . ')/is', function($r) {
            return $this->parseTag($r[2]);
        }, $content);
        return $content;
    }

    // ���PHP�﷨
    protected function parsePhp($content) {
        if (ini_get('short_open_tag')) {
            // �����̱�ǩ�����Ҫ��<?��ǩ��echo��ʽ��� �����޷��������xml��ʶ
            $content = preg_replace('/(<\?(?!php|=|$))/i', '<?php echo \'\\1\'; ?>' . "\n", $content);
        }
        // PHP�﷨���
        if (C('TMPL_DENY_PHP') && false !== strpos($content, '<?php')) {
            throw_exception(L('_NOT_ALLOW_PHP_'));
        }
        return $content;
    }

    // ����ģ���еĲ��ֱ�ǩ
    protected function parseLayout($content) {
        // ��ȡģ���еĲ��ֱ�ǩ
        $find = preg_match('/' . $this->config['taglib_begin'] . 'layout\s(.+?)\s*?\/' . $this->config['taglib_end'] . '/is', $content, $matches);
        if ($find) {
            //�滻Layout��ǩ
            $content = str_replace($matches[0], '', $content);
            //����Layout��ǩ
            $array = $this->parseXmlAttrs($matches[1]);
            if (!C('LAYOUT_ON') || C('LAYOUT_NAME') != $array['name']) {
                // ��ȡ����ģ��
                $layoutFile = THEME_PATH . $array['name'] . $this->config['template_suffix'];
                $replace = isset($array['replace']) ? $array['replace'] : $this->config['layout_item'];
                // �滻���ֵ���������
                $content = str_replace($replace, $content, file_get_contents($layoutFile));
            }
        } else {
            $content = str_replace('{__NOLAYOUT__}', '', $content);
        }
        return $content;
    }

    // ����ģ���е�include��ǩ
    protected function parseInclude($content) {
        // �����̳�
        $content = $this->parseExtend($content);
        // ��������
        $content = $this->parseLayout($content);
        // ��ȡģ���е�include��ǩ
        $find = preg_match_all('/' . $this->config['taglib_begin'] . 'include\s(.+?)\s*?\/' . $this->config['taglib_end'] . '/is', $content, $matches);
        if ($find) {
            for ($i = 0; $i < $find; $i++) {
                $include = $matches[1][$i];
                $array = $this->parseXmlAttrs($include);
                $file = $array['file'];
                unset($array['file']);
                $content = str_replace($matches[0][$i], $this->parseIncludeItem($file, $array), $content);
            }
        }
        return $content;
    }

    // ����ģ���е�extend��ǩ
    protected function parseExtend($content) {
        $begin = $this->config['taglib_begin'];
        $end = $this->config['taglib_end'];
        // ��ȡģ���еļ̳б�ǩ
        $find = preg_match('/' . $begin . 'extend\s(.+?)\s*?\/' . $end . '/is', $content, $matches);
        if ($find) {
            //�滻extend��ǩ
            $content = str_replace($matches[0], '', $content);
            // ��¼ҳ���е�block��ǩ
            //preg_replace('/' . $begin . 'block\sname=(.+?)\s*?' . $end . '(.*?)' . $begin . '\/block' . $end . '/eis', "\$this->parseBlock('\\1','\\2')", $content);
            preg_replace_callback('/' . $begin . 'block\sname=(.+?)\s*?' . $end . '(.*?)' . $begin . '\/block' . $end . '/is', function ($r) {
                $this->parseBlock($r[1], $r[2]);
            }, $content);
            // ��ȡ�̳�ģ��
            $array = $this->parseXmlAttrs($matches[1]);
            $content = $this->parseTemplateName($array['name']);
            // �滻block��ǩ
            //$content = preg_replace('/' . $begin . 'block\sname=(.+?)\s*?' . $end . '(.*?)' . $begin . '\/block' . $end . '/eis', "\$this->replaceBlock('\\1','\\2')", $content);
            $content = preg_replace_callback('/' . $begin . 'block\sname=(.+?)\s*?' . $end . '(.*?)' . $begin . '\/block' . $end . '/is', function ($r) {
                return replaceBlock($r[1], $r[2]);
            }, $content);
        } else {
            //$content    =   preg_replace('/'.$begin.'block\sname=(.+?)\s*?'.$end.'(.*?)'.$begin.'\/block'.$end.'/eis',"stripslashes('\\2')",$content);  
            $content = preg_replace_callback('/' . $begin . 'block\sname=(.+?)\s*?' . $end . '(.*?)' . $begin . '\/block' . $end . '/is', function ($r) {
                return stripslashes($r[2]);
            }, $content);
        }
        return $content;
    }

    /**
     * ����XML����
     * @access private
     * @param string $attrs  XML�����ַ���
     * @return array
     */
    private function parseXmlAttrs($attrs) {
        $xml = '<tpl><tag ' . $attrs . ' /></tpl>';
        $xml = simplexml_load_string($xml);
        if (!$xml)
            throw_exception(L('_XML_TAG_ERROR_'));
        $xml = (array) ($xml->tag->attributes());
        $array = array_change_key_case($xml['@attributes']);
        return $array;
    }

    /**
     * �滻ҳ���е�literal��ǩ
     * @access private
     * @param string $content  ģ������
     * @return string|false
     */
    private function parseLiteral($content) {
        if (trim($content) == '')
            return '';
        $content = stripslashes($content);
        $i = count($this->literal);
        $parseStr = "<!--###literal{$i}###-->";
        $this->literal[$i] = $content;
        return $parseStr;
    }

    /**
     * ��ԭ���滻��literal��ǩ
     * @access private
     * @param string $tag  literal��ǩ���
     * @return string|false
     */
    private function restoreLiteral($tag) {
        // ��ԭliteral��ǩ
        $parseStr = $this->literal[$tag];
        // ����literal��¼
        unset($this->literal[$tag]);
        return $parseStr;
    }

    /**
     * ��¼��ǰҳ���е�block��ǩ
     * @access private
     * @param string $name block����
     * @param string $content  ģ������
     * @return string
     */
    private function parseBlock($name, $content) {
        $this->block[$name] = $content;
        return '';
    }

    /**
     * �滻�̳�ģ���е�block��ǩ
     * @access private
     * @param string $name  block����
     * @param string $content  ģ������
     * @return string
     */
    private function replaceBlock($name, $content) {
        // �滻block��ǩ û�����¶�����ʹ��ԭ����
        $replace = isset($this->block[$name]) ? $this->block[$name] : $content;
        return stripslashes($replace);
    }

    /**
     * ����ģ��ҳ���а�����TagLib��
     * �������б�
     * @access public
     * @param string $content  ģ������
     * @return string|false
     */
    public function getIncludeTagLib(& $content) {
        //�����Ƿ���TagLib��ǩ
        $find = preg_match('/' . $this->config['taglib_begin'] . 'taglib\s(.+?)(\s*?)\/' . $this->config['taglib_end'] . '\W/is', $content, $matches);
        if ($find) {
            //�滻TagLib��ǩ
            $content = str_replace($matches[0], '', $content);
            //����TagLib��ǩ
            $array = $this->parseXmlAttrs($matches[1]);
            $this->tagLib = explode(',', $array['name']);
        }
        return;
    }

    /**
     * TagLib�����
     * @access public
     * @param string $tagLib Ҫ�����ı�ǩ��
     * @param string $content Ҫ������ģ������
     * @param boolen $hide �Ƿ����ر�ǩ��ǰ׺
     * @return string
     */
    public function parseTagLib($tagLib, &$content, $hide = false) {
        $begin = $this->config['taglib_begin'];
        $end = $this->config['taglib_end'];
        $className = 'TagLib' . ucwords($tagLib);
        $tLib = Think::instance($className);
        foreach ($tLib->getTags() as $name => $val) {
            $tags = array($name);
            if (isset($val['alias'])) {// ��������
                $tags = explode(',', $val['alias']);
                $tags[] = $name;
            }
            $level = isset($val['level']) ? $val['level'] : 1;
            $closeTag = isset($val['close']) ? $val['close'] : true;
            foreach ($tags as $tag) {
                $parseTag = !$hide ? $tagLib . ':' . $tag : $tag; // ʵ��Ҫ�����ı�ǩ����
                if (!method_exists($tLib, '_' . $tag)) {
                    // �����������趨���������
                    $tag = $name;
                }
                $n1 = empty($val['attr']) ? '(\s*?)' : '\s([^' . $end . ']*)';
                if (!$closeTag) {
                    $patterns = '/' . $begin . $parseTag . $n1 . '\/(\s*?)' . $end . '/is';
                    $replacement = "\$this->parseXmlTag('$tagLib','$tag','$1','')";
                    //$content = preg_replace($patterns, $replacement, $content);
					$content = preg_replace_callback($patterns, function($r) use ($tagLib,$tag) {
						return $this->parseXmlTag($tagLib, $tag, $r[1], '');
					},$content);
                } else {
                    $patterns = '/' . $begin . $parseTag . $n1 . $end . '(.*?)' . $begin . '\/' . $parseTag . '(\s*?)' . $end . '/is';
                    $replacement = "\$this->parseXmlTag('$tagLib','$tag','$1','$2')";
                    for ($i = 0; $i < $level; $i++)
                    //$content=preg_replace($patterns,$replacement,$content);
					$content = preg_replace_callback($patterns,  function($r) use($tagLib,$tag) {
						return $this->parseXmlTag($tagLib, $tag, $r[1], $r[2]);
					},$content);
                }
            }
        }
    }

    /**
     * ������ǩ��ı�ǩ
     * ��Ҫ���ö�Ӧ�ı�ǩ���ļ�������
     * @access public
     * @param string $tagLib  ��ǩ������
     * @param string $tag  ��ǩ��
     * @param string $attr  ��ǩ����
     * @param string $content  ��ǩ����
     * @return string|false
     */
    public function parseXmlTag($tagLib, $tag, $attr, $content) {
        //if (MAGIC_QUOTES_GPC) {
        $attr = stripslashes($attr);
        $content = stripslashes($content);
        //}
        if (ini_get('magic_quotes_sybase'))
            $attr = str_replace('\"', '\'', $attr);
        $tLib = Think::instance('TagLib' . ucwords(strtolower($tagLib)));
        $parse = '_' . $tag;
        $content = trim($content);
        return $tLib->$parse($attr, $content);
    }

    /**
     * ģ���ǩ����
     * ��ʽ�� {TagName:args [|content] }
     * @access public
     * @param string $tagStr ��ǩ����
     * @return string
     */
    public function parseTag($tagStr) {
        //if (MAGIC_QUOTES_GPC) {
        $tagStr = stripslashes($tagStr);
        //}
        //��ԭ��ģ���ǩ
        if (preg_match('/^[\s|\d]/is', $tagStr))
        //���˿ո�����ִ�ͷ�ı�ǩ
            return C('TMPL_L_DELIM') . $tagStr . C('TMPL_R_DELIM');
        $flag = substr($tagStr, 0, 1);
        $flag2 = substr($tagStr, 1, 1);
        $name = substr($tagStr, 1);
        if ('$' == $flag && '.' != $flag2 && '(' != $flag2) { //����ģ����� ��ʽ {$varName}
            return $this->parseVar($name);
        } elseif ('-' == $flag || '+' == $flag) { // �������
            return '<?php echo ' . $flag . $name . ';?>';
        } elseif (':' == $flag) { // ���ĳ�������Ľ��
            return '<?php echo ' . $name . ';?>';
        } elseif ('~' == $flag) { // ִ��ĳ������
            return '<?php ' . $name . ';?>';
        } elseif (substr($tagStr, 0, 2) == '//' || (substr($tagStr, 0, 2) == '/*' && substr($tagStr, -2) == '*/')) {
            //ע�ͱ�ǩ
            return '';
        }
        // δʶ��ı�ǩֱ�ӷ���
        return C('TMPL_L_DELIM') . $tagStr . C('TMPL_R_DELIM');
    }

    /**
     * ģ���������,֧��ʹ�ú���
     * ��ʽ�� {$varname|function1|function2=arg1,arg2}
     * @access public
     * @param string $varStr ��������
     * @return string
     */
    public function parseVar($varStr) {
        $varStr = trim($varStr);
        static $_varParseList = array();
        //����Ѿ��������ñ����ִ�����ֱ�ӷ��ر���ֵ
        if (isset($_varParseList[$varStr]))
            return $_varParseList[$varStr];
        $parseStr = '';
        $varExists = true;
        if (!empty($varStr)) {
            $varArray = explode('|', $varStr);
            //ȡ�ñ�������
            $var = array_shift($varArray);
            if ('Think.' == substr($var, 0, 6)) {
                // ������Think.��ͷ������������Դ� ����ģ�帳ֵ�Ϳ������
                $name = $this->parseThinkVar($var);
            } elseif (false !== strpos($var, '.')) {
                //֧�� {$var.property}
                $vars = explode('.', $var);
                $var = array_shift($vars);
                switch (strtolower(C('TMPL_VAR_IDENTIFY'))) {
                    case 'array': // ʶ��Ϊ����
                        $name = '$' . $var;
                        foreach ($vars as $key => $val)
                            $name .= '["' . $val . '"]';
                        break;
                    case 'obj':  // ʶ��Ϊ����
                        $name = '$' . $var;
                        foreach ($vars as $key => $val)
                            $name .= '->' . $val;
                        break;
                    default:  // �Զ��ж��������� ֻ֧�ֶ�ά
                        $name = 'is_array($' . $var . ')?$' . $var . '["' . $vars[0] . '"]:$' . $var . '->' . $vars[0];
                }
            } elseif (false !== strpos($var, '[')) {
                //֧�� {$var['key']} ��ʽ�������
                $name = "$" . $var;
                preg_match('/(.+?)\[(.+?)\]/is', $var, $match);
                $var = $match[1];
            } elseif (false !== strpos($var, ':') && false === strpos($var, '(') && false === strpos($var, '::') && false === strpos($var, '?')) {
                //֧�� {$var:property} ��ʽ������������
                $vars = explode(':', $var);
                $var = str_replace(':', '->', $var);
                $name = "$" . $var;
                $var = $vars[0];
            } else {
                $name = "$$var";
            }
            //�Ա���ʹ�ú���
            if (count($varArray) > 0)
                $name = $this->parseVarFunction($name, $varArray);
            $parseStr = '<?php echo (' . $name . '); ?>';
        }
        $_varParseList[$varStr] = $parseStr;
        return $parseStr;
    }

    /**
     * ��ģ�����ʹ�ú���
     * ��ʽ {$varname|function1|function2=arg1,arg2}
     * @access public
     * @param string $name ������
     * @param array $varArray  �����б�
     * @return string
     */
    public function parseVarFunction($name, $varArray) {
        //�Ա���ʹ�ú���
        $length = count($varArray);
        //ȡ��ģ���ֹʹ�ú����б�
        $template_deny_funs = explode(',', C('TMPL_DENY_FUNC_LIST'));
        for ($i = 0; $i < $length; $i++) {
            $args = explode('=', $varArray[$i], 2);
            //ģ�庯������
            $fun = strtolower(trim($args[0]));
            switch ($fun) {
                case 'default':  // ����ģ�庯��
                    $name = '(' . $name . ')?(' . $name . '):' . $args[1];
                    break;
                default:  // ͨ��ģ�庯��
                    if (!in_array($fun, $template_deny_funs)) {
                        if (isset($args[1])) {
                            if (strstr($args[1], '###')) {
                                $args[1] = str_replace('###', $name, $args[1]);
                                $name = "$fun($args[1])";
                            } else {
                                $name = "$fun($name,$args[1])";
                            }
                        } else if (!empty($args[0])) {
                            $name = "$fun($name)";
                        }
                    }
            }
        }
        return $name;
    }

    /**
     * ����ģ���������
     * ��ʽ �� $Think. ��ͷ�ı�����������ģ�����
     * @access public
     * @param string $varStr  �����ַ���
     * @return string
     */
    public function parseThinkVar($varStr) {
        $vars = explode('.', $varStr);
        $vars[1] = strtoupper(trim($vars[1]));
        $parseStr = '';
        if (count($vars) >= 3) {
            $vars[2] = trim($vars[2]);
            switch ($vars[1]) {
                case 'SERVER':
                    $parseStr = '$_SERVER[\'' . strtoupper($vars[2]) . '\']';
                    break;
                case 'GET':
                    $parseStr = '$_GET[\'' . $vars[2] . '\']';
                    break;
                case 'POST':
                    $parseStr = '$_POST[\'' . $vars[2] . '\']';
                    break;
                case 'COOKIE':
                    if (isset($vars[3])) {
                        $parseStr = '$_COOKIE[\'' . $vars[2] . '\'][\'' . $vars[3] . '\']';
                    } else {
                        $parseStr = 'cookie(\'' . $vars[2] . '\')';
                    }
                    break;
                case 'SESSION':
                    if (isset($vars[3])) {
                        $parseStr = '$_SESSION[\'' . $vars[2] . '\'][\'' . $vars[3] . '\']';
                    } else {
                        $parseStr = 'session(\'' . $vars[2] . '\')';
                    }
                    break;
                case 'ENV':
                    $parseStr = '$_ENV[\'' . strtoupper($vars[2]) . '\']';
                    break;
                case 'REQUEST':
                    $parseStr = '$_REQUEST[\'' . $vars[2] . '\']';
                    break;
                case 'CONST':
                    $parseStr = strtoupper($vars[2]);
                    break;
                case 'LANG':
                    $parseStr = 'L("' . $vars[2] . '")';
                    break;
                case 'CONFIG':
                    if (isset($vars[3])) {
                        $vars[2] .= '.' . $vars[3];
                    }
                    $parseStr = 'C("' . $vars[2] . '")';
                    break;
                default:break;
            }
        } else if (count($vars) == 2) {
            switch ($vars[1]) {
                case 'NOW':
                    $parseStr = "date('Y-m-d g:i a',time())";
                    break;
                case 'VERSION':
                    $parseStr = 'THINK_VERSION';
                    break;
                case 'TEMPLATE':
                    $parseStr = "'" . $this->templateFile . "'"; //'C("TEMPLATE_NAME")';
                    break;
                case 'LDELIM':
                    $parseStr = 'C("TMPL_L_DELIM")';
                    break;
                case 'RDELIM':
                    $parseStr = 'C("TMPL_R_DELIM")';
                    break;
                default:
                    if (defined($vars[1]))
                        $parseStr = $vars[1];
            }
        }
        return $parseStr;
    }

    /**
     * ���ع���ģ�岢���� �͵�ǰģ����ͬһ·��������ʹ�����·��
     * @access private
     * @param string $tmplPublicName  ����ģ���ļ���
     * @param array $vars  Ҫ���ݵı����б�
     * @return string
     */
    private function parseIncludeItem($tmplPublicName, $vars = array()) {
        // ����ģ���ļ�������ȡ����
        $parseStr = $this->parseTemplateName($tmplPublicName);
        // �滻����
        foreach ($vars as $key => $val) {
            $parseStr = str_replace('[' . $key . ']', $val, $parseStr);
        }
        // �ٴζ԰����ļ�����ģ�����
        return $this->parseInclude($parseStr);
    }

    /**
     * �������ص�ģ���ļ�����ȡ���� ֧�ֶ��ģ���ļ���ȡ
     * @access private
     * @param string $tmplPublicName  ģ���ļ���
     * @return string
     */
    private function parseTemplateName($templateName) {
        if (substr($templateName, 0, 1) == '$')
        //֧�ּ��ر����ļ���
            $templateName = $this->get(substr($templateName, 1));
        $array = explode(',', $templateName);
        $parseStr = '';
        foreach ($array as $templateName) {
            if (false === strpos($templateName, $this->config['template_suffix'])) {
                // ��������Ϊ ����@ģ������:ģ��:����
                if (strpos($templateName, '@')) {
                    list($group, $templateName) = explode('@', $templateName);
                    if (1 == C('APP_GROUP_MODE')) {
                        $basePath = dirname(BASE_LIB_PATH) . '/' . $group . '/' . basename(TMPL_PATH) . '/' . (THEME_NAME ? THEME_NAME . '/' : '');
                    } else {
                        $basePath = TMPL_PATH . '/' . $group . '/' . (THEME_NAME ? THEME_NAME . '/' : '');
                    }
                } else {
                    $basePath = THEME_PATH;
                }
                $templateName = str_replace(':', '/', $templateName);
                $path = explode('/', $templateName);
                $action = array_pop($path);
                $module = !empty($path) ? array_pop($path) : MODULE_NAME;
                if (!empty($path)) {// ����ģ������
                    $basePath = dirname($basePath) . '/' . array_pop($path) . '/';
                }
                $templateName = $basePath . $module . C('TMPL_FILE_DEPR') . $action . $this->config['template_suffix'];
            }
            // ��ȡģ���ļ�����
            $parseStr .= file_get_contents($templateName);
        }
        return $parseStr;
    }

}
