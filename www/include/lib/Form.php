<?php

/**
 * Created by PhpStorm.
 * User: mgckid
 * Date: 2022/2/4
 * Time: 10:41
 */

class Form
{
    const layui_form = 'LayuiForm';
    const autocomplete_on = 'on';
    const autocomplete_off = 'off';
    const verify_required = 'required'; //（必填项）
    const verify_phone = 'phone'; //（手机号）
    const verify_email = 'email'; //（邮箱）
    const verify_url = 'url'; //（网址）
    const verify_number = 'number'; //（数字）
    const verify_date = 'date'; //（日期）
    const verify_identity = 'identity'; //（身份证）
    const form_method_post = 'post';
    const form_method_get = 'get';

    public $config = array(
        'form_id' => '',
        'form_method' => 'post',
        'form_action' => '',
        'form_class' => array()
    );
    private $inline = false;
    private $inline_schema = array();
    public $schema = array();
    private $table_schema = array();
    public $data = array();
    public $display_none_field = array();

    private static $instance;

    private function __construct()
    {
        $this->config['form_action'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //文本输入框
    public function input_text($title, $description, $name, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'text',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //范围输入框
    public function input_range($title, $description, $name, $value = array(), $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'range',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    public function input_hidden($name, $value = '')
    {
        $init = array(
            'type' => 'hidden',
            'name' => $name,
            'value' => $value,
        );
        $this->schema[] = $init;
        return $this;
    }

    //日期输入框
    public function input_date($title, $description, $name, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'date',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //密码输入框
    public function input_password($title, $description, $name, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'password',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    public function empty_item($title, $description, $name)
    {
        $init = array(
            'type' => 'empty_box',
            'name' => $name,
            'title' => $title,
            'description' => $description,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    public function empty_box($title, $description, $name)
    {
        return $this->empty_item($title, $description, $name);
    }

    //复选框
    public function checkbox($title, $description, $name, array $enum, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'checkbox',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'enum' => $enum,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //开关
    public function addSwitch($title, $description, $name, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'switch',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //单选框
    public function radio($title, $description, $name, array $enum, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'radio',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'enum' => $enum,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //下拉选项
    public function select($title, $description, $name, array $enum, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'select',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'enum' => $enum,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //编辑框
    public function textarea($title, $description, $name, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'textarea',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //编辑器
    public function editor($title, $description, $name, $value = '', $disabled = false, $autocomplete = self::autocomplete_off, $verify = array(self::verify_required))
    {
        $init = array(
            'type' => 'editor',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'id' => $name,
            'disabled' => $disabled,
            'autocomplete' => $autocomplete,
            'verify' => $verify,
            'value' => $value,
        );
        if ($this->inline) {
            $this->inline_schema[] = $init;
        } else {
            $this->schema[] = $init;
        }
        return $this;
    }

    //表格
    public function table($title, $description, $name, array $init, array $value = array())
    {
        $init = array(
            'type' => 'table',
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'init' => $init,
            'value' => $value,
        );
        $this->schema[] = $init;
        return $this;
    }

    //行内开始
    public function input_inline_start()
    {
        $this->inline = true;
        return $this;
    }

    //行内结束
    public function input_inline_end()
    {
        $this->schema[] = $this->inline_schema;
        $this->inline_schema = array();
        $this->inline = false;
        return $this;
    }

    //表单类Class
    public function form_class($class_name)
    {
        $class_name = is_scalar($class_name) ? array($class_name) : $class_name;
        $this->config['form_class'] = array_merge($this->config['form_class'], $class_name);
        return $this;
    }

    //表单ID
    public function form_id($id_name)
    {
        $this->config['form_id'] = $id_name;
        return $this;
    }

    //表单定向地址
    public function form_action($form_action)
    {
        $this->config['form_action'] = $form_action;
        return $this;
    }

    //表单提交方式GET、POST
    public function form_method($form_method)
    {
        $this->config['form_method'] = $form_method;
        return $this;
    }

    //初始化表单
    public function form_init(array $init_data)
    {
        if ($this->inline) {
            $this->inline_schema = array_merge($this->inline_schema, $init_data);
        } else {
            $this->schema = array_merge($this->schema, $init_data);
        }
        return $this;
    }

    public function form_schema(array $init_data)
    {
        return $this->form_init($init_data);
    }

    public function form_data(array $form_data)
    {
        $this->data = $form_data;
        return $this;
    }

    public function input_submit($title, $submit_btn_raw_text = '', $reset_btn_raw_text = '', $display_none_show_btn_raw_text = '')
    {
        $init = array(
            'type' => 'submit',
            'title' => $title,
            'raw_text' => $submit_btn_raw_text,
            'reset_btn_raw_text' => $reset_btn_raw_text,
            'display_none_show_btn_raw_text' => $display_none_show_btn_raw_text,
        );
        if ($this->inline) {
            $this->inline_schema = array_merge($this->inline_schema, array($init));
        } else {
            $this->schema = array_merge($this->schema, array($init));
        }
        return $this;
    }

    public function create($form_type = self::layui_form)
    {
        return call_user_func_array(array(new $form_type(), 'create'), array($this));
    }

    //判断表单类型是否存在
    public function type_in($form_type)
    {
        if (!$this->schema) {
            return false;
        }
        $_type = array();
        foreach ($this->schema as $value) {
            if (isset($value[0])) {
                foreach ($value as $val) {
                    $_type[] = isset($val['type']) ? $val['type'] : '';
                }
            } else {
                $_type[] = isset($value['type']) ? $value['type'] : '';
            }
        }
        if (in_array($form_type, $_type)) {
            return true;
        } else {
            return false;
        }
    }

    public function assign_display_none_field($field_name)
    {
        if (is_scalar($field_name)) {
            $this->display_none_field[] = $field_name;
        } else {
            $this->display_none_field = array_merge($this->display_none_field, $field_name);
        }
        return $this;
    }
}

/**
 * Class LayuiForm
 * @property Form $form_instance
 */
class LayuiForm
{
    const form_class_pane = 'layui-form-pane';
    private $form_instance;

    public function create(Form $formObj)
    {
        $this->form_instance = $formObj;
        //渲染html
        $formObj->schema = array_values(isset($formObj->schema) ? $formObj->schema : array());
        $item_html = array();
        foreach ($formObj->schema as $item) {
            $is_block = isset($item['type']) ? true : false;
            if ($is_block) {
                $item_html[] = $this->render_item_block($item);
            } else {
                $item_html[] = $this->render_item_inline($item);
            }
        }
        $item_html = join(PHP_EOL, $item_html);
        $form_id = $formObj->config['form_id'] ? 'id="' . $formObj->config['form_id'] . '"' : '';
        $form_action = $formObj->config['form_action'] ? 'action="' . $formObj->config['form_action'] . '"' : '';
        $form_method = $formObj->config['form_method'] ? 'method="' . $formObj->config['form_method'] . '"' : '';
        $form_class = $formObj->config['form_class'] ? 'class="' . join(' ', array_merge(array('layui-form'), $formObj->config['form_class'])) . '"' : '';
        $html = <<<ST
            <form {$form_class} {$form_id} {$form_action} {$form_method}>
            {$item_html}
ST;
        return $html;
    }

    private function render_item_block($init_data)
    {
        if (isset($init_data['name']) and isset($this->form_instance->data[$init_data['name']])) {
            $init_data['value'] = $this->form_instance->data[$init_data['name']];
        }
        $input_type = isset($init_data['type']) ? $init_data['type'] : '';
        $description = isset($init_data['description']) ? $init_data['description'] : '';
        $tip_html = $description ? "<tip>{$description}</tip>" : '';
        $input_html = $this->render_input($init_data, isset($init_data['value']) ? $init_data['value'] : '');
        if (strtolower($input_type) == 'hidden') {
            $block_html = <<<ST
            {$input_html}
ST;
        } elseif (strtolower($input_type) == 'none') {
            $block_html = '';
        } elseif (strtolower($input_type) == 'submit') {
            $block_html = <<<ST
                <div class="layui-form-item">
                    <div class="layui-input-block">
                     {$input_html}
                     </div>
                </div>
ST;
        } else {
            $label_text = isset($init_data['title']) ? $init_data['title'] : '';
            $block_html = <<<ST
                <div class="layui-form-item">
                    <label class="layui-form-label">{$label_text}</label>
                    <div class="layui-input-block">
                        {$input_html}
                        $tip_html
                    </div>
                </div>
ST;
        }
        return $block_html;
    }

    private function render_item_inline($item_datas)
    {
        $inline_html = array();
        foreach ($item_datas as $init_data) {
            if (isset($init_data['name']) and isset($this->form_instance->data[$init_data['name']])) {
                $init_data['value'] = $this->form_instance->data[$init_data['name']];
            } else {
                $init_data['value'] = '';
            }
            $input_type = isset($init_data['type']) ? $init_data['type'] : '';
            if (strtolower($input_type) == 'hidden') {
                $input_html = $this->render_input($init_data, isset($init_data['value']) ? $init_data['value'] : '');
                $html = $input_html;
            } elseif (strtolower($input_type) == 'none') {
                $html = '';
            } elseif (strtolower($input_type) == 'submit') {
                $input_html = $this->render_input($init_data, isset($init_data['value']) ? $init_data['value'] : '');
                $html = <<<ST
                <div class="layui-inline">
                     {$input_html}
                </div>
ST;
            } elseif (strtolower($input_type) == 'range') {
                $display_none_css_str = '';
                $display_none_class_str = '';
                if (in_array($init_data['name'], $this->form_instance->display_none_field)) {
                    $display_none_css_str = "style=\"display:none\"";
                    $display_none_class_str = 'inline_display_none_tag';
                }
                $init_data['name'] = $init_data['name'] . "[]";
                $input_html1 = $this->render_input($init_data, isset($init_data['value'][0]) ? $init_data['value'][0] : '');
                $input_html2 = $this->render_input($init_data, isset($init_data['value'][1]) ? $init_data['value'][1] : '');
                $label_text = isset($init_data['title']) ? $init_data['title'] : '';
                $html = <<<str
                  <div class="layui-inline {$display_none_class_str}" {$display_none_css_str}>
                    <label class="layui-form-label">{$label_text}</label>
                    <div class="layui-input-inline" style="width: 100px;">
                      {$input_html1}
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 100px;">
                      {$input_html2}
                    </div>
                  </div>
str;
            } else {
                $display_none_css_str = '';
                $display_none_class_str = '';
                if (in_array($init_data['name'], $this->form_instance->display_none_field)) {
                    $display_none_css_str = "style=\"display:none\"";
                    $display_none_class_str = 'inline_display_none_tag';
                }
                $input_html = $this->render_input($init_data, isset($init_data['value']) ? $init_data['value'] : '');
                $label_text = isset($init_data['title']) ? $init_data['title'] : '';
                $html = <<<str
                    <div class="layui-inline {$display_none_class_str}" {$display_none_css_str}>
                        <label class="layui-form-label">{$label_text}</label>
                        <div class="layui-input-inline">
                            {$input_html}
                        </div>
                    </div>
str;
            }
            $inline_html[] = $html;
        }
        $inline_html = join(PHP_EOL, $inline_html);
        $block_html = <<<ST
            <div class="layui-form-item">
               {$inline_html}
            </div>
ST;
        return $block_html;
    }

    private function render_input($init_data, $value)
    {
        $init_data['type'] = isset($init_data['type']) ? $init_data['type'] : '';
        $init_data['name'] = isset($init_data['name']) ? $init_data['name'] : '';
        $init_data['title'] = isset($init_data['title']) ? $init_data['title'] : '';
        $init_data['enum'] = isset($init_data['enum']) ? $init_data['enum'] : array();
        $init_data['disabled'] = isset($init_data['disabled']) ? $init_data['disabled'] : false;

        if ($init_data['type'] == 'submit') {
            $init_data['raw_text'] = isset($init_data['raw_text']) ? $init_data['raw_text'] : '';
            $init_data['reset_btn_raw_text'] = isset($init_data['reset_btn_raw_text']) ? $init_data['reset_btn_raw_text'] : '';
            $init_data['display_none_show_btn_raw_text'] = isset($init_data['display_none_show_btn_raw_text']) ? $init_data['display_none_show_btn_raw_text'] : '';

            if ($init_data['reset_btn_raw_text']) {
                $reset_html = <<<STR
    <button type="reset" {$init_data['reset_btn_raw_text']} >重置</button>
STR;
            } else {
                $reset_html = '';
            }

            if (is_array($this->form_instance->display_none_field) && count(array_filter($this->form_instance->display_none_field))) {
                $display_none_show_btn_html = <<<STR
    <button type="button" {$init_data['display_none_show_btn_raw_text']} >高级搜索 ></button>
STR;
            } else {
                $display_none_show_btn_html = '';
            }

            $html = <<<str
            <button type="submit" {$init_data['raw_text']} >{$init_data['title']}</button>
           {$reset_html}
           {$display_none_show_btn_html}
str;
        } elseif ($init_data['type'] == 'text') {
            $disabled_str = $init_data['disabled'] ? 'disabled' : '';
            $html = array();
            $value = is_array($value) ? $value : array($value);
            foreach ($value as $ke => $val) {
                if (count($value) > 1) {
                    $name_str = "{$init_data['name']}[{$ke}]";
                    $name_verify = isset($init_data['verify'][$ke]) ? "{$init_data['verify'][$ke]}" : "";
                    $name_placeholder = isset($init_data['placeholder'][$ke]) ? "{$init_data['placeholder'][$ke]}" : "";
                } else {
                    $name_str = $init_data['name'];
                    $name_verify = isset($init_data['verify']) ? is_array($init_data['verify']) ? join(',', $init_data['verify']) : $init_data['verify'] : "";
                    $name_placeholder = isset($init_data['placeholder']) ? $init_data['placeholder'] : "";
                }
                $html[] = <<<str
<input name="{$name_str}" value="{$val}" type="text"  lay-verify="{$name_verify}"  placeholder="{$name_placeholder}"   class="layui-input"  {$disabled_str}/>
str;
            }
            $html = join("", $html);
        } elseif ($init_data['type'] == 'date') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $value = is_array($value) ? $value : array($value);

            $disabled_str = $init_data['disabled'] ? 'disabled' : '';
            $html = array();
            foreach ($value as $val) {
                $html[] = <<<str
<input {$name_str}  class="layui-input"  {$disabled_str} value="{$val}" type="text"  input_type="date"/>
str;
            }
            $html = join("\n", $html);
        } elseif ($init_data['type'] == 'color') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $value = is_array($value) ? $value : array($value);
            $disabled_str = $init_data['disabled'] ? 'disabled' : '';
            $html = array();
            foreach ($value as $val) {
                $html[] = <<<str
                <input type="text" class="coloris form-control"  placeholder="请选择颜色" {$disabled_str} {$name_str} value="{$val}">
str;
            }
            $html = join("\n", $html);
        } elseif ($init_data['type'] == 'password') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $html = <<<STR
            <input {$name_str} value="{$value}" type="password"  class="layui-input"  />
STR;
        } elseif ($init_data['type'] == 'hidden') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $html = <<<STR
        <input {$name_str} value="{$value}" type="hidden" />
STR;
        } elseif ($init_data['type'] == 'empty_box') {
            $name_str = $init_data['name'] ? "id=\"{$init_data['name']}\"" : '';
            $html = <<<STR
        <div {$name_str}></div>
STR;
        } elseif ($init_data['type'] == 'select') {
            $disabled_str = $init_data['disabled'] ? 'disabled' : '';
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $init_data['enum'] = isset($init_data['enum']) ? $init_data['enum'] : array();
            $enum = array();
            foreach ($init_data['enum'] as $key => $item) {
                if (is_array($item)) {
                    $item['value'] = isset($item['value']) ? $item['value'] : '';
                    $item['name'] = isset($item['name']) ? $item['name'] : '';
                } elseif (is_scalar($item)) {
                    $_name = $item;
                    $item = array();
                    $item['value'] = $key;
                    $item['name'] = $_name;
                } else {
                    throw new Exception('枚举值错误');
                }
                $checked = $item['value'] == $value ? 'selected' : '';
                $enum[] = '<option value="' . $item['value'] . '" ' . $checked . '>' . $item['name'] . '</option>';
            }
            $enum = join("\n", $enum);
            $html = <<<STR
            <select {$name_str} {$disabled_str} lay-search>
                <option value=""></option>
                {$enum}
            </select>
STR;
        } elseif ($init_data['type'] == 'select_multi') {
            $disabled_str = $init_data['disabled'] ? 'disabled' : '';
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $init_data['enum'] = isset($init_data['enum']) ? $init_data['enum'] : array();
            $enum = array();
            $value = str_replace('|', ',', $value);
            $value = is_scalar($value) ? explode(',', $value) : $value;
            $value = is_array($value) ? $value : array($value);
            foreach ($init_data['enum'] as $key => $item) {
                if (is_array($item)) {
                    $item['value'] = isset($item['value']) ? $item['value'] : '';
                    $item['name'] = isset($item['name']) ? $item['name'] : '';
                } elseif (is_scalar($item)) {
                    $_name = $item;
                    $item = array();
                    $item['value'] = $key;
                    $item['name'] = $_name;
                } else {
                    throw new Exception('枚举值错误');
                }
                $checked = in_array($item['value'], $value) ? 'selected' : '';
                $enum[] = '<option value="' . $item['value'] . '" ' . $checked . '>' . $item['name'] . '</option>';
            }
            $enum = join("\n", $enum);
            $html = <<<STR
            <select {$name_str} {$disabled_str} lay-search multiple>
                <option value=""></option>
                {$enum}
            </select>
STR;
        } elseif ($init_data['type'] == 'radio') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $init_data['enum'] = isset($init_data['enum']) ? $init_data['enum'] : array();
            $value = (string) $value;
            $enum = array();
            foreach ($init_data['enum'] as $key => $item) {
                if (is_array($item)) {
                    $item['value'] = isset($item['value']) ? $item['value'] : '';
                    $item['name'] = isset($item['name']) ? $item['name'] : '';
                } elseif (is_scalar($item)) {
                    $_name = $item;
                    $item = array();
                    $item['value'] = $key;
                    $item['name'] = $_name;
                } else {
                    throw new Exception('枚举值错误');
                }
                $checked = $item['value'] == $value ? 'checked' : '';
                $enum[] = "<input type=\"radio\" {$name_str} value=\"{$item['value']}\" title=\"{$item['name']}\" {$checked}/>";
            }
            $enum = join("\n", $enum);
            $html = <<<STR
             {$enum}
STR;
        } elseif ($init_data['type'] == 'checkbox') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}[]\"" : '';
            $init_data['enum'] = isset($init_data['enum']) ? $init_data['enum'] : array();
            $value = str_replace('|', ',', $value);
            $value = is_scalar($value) ? explode(',', $value) : $value;
            $value = is_array($value) ? $value : array($value);
            $enum = array();
            foreach ($init_data['enum'] as $key => $item) {
                if (is_array($item)) {
                    $item['value'] = isset($item['value']) ? $item['value'] : '';
                    $item['name'] = isset($item['name']) ? $item['name'] : '';
                } elseif (is_scalar($item)) {
                    $_name = $item;
                    $item = array();
                    $item['value'] = $key;
                    $item['name'] = $_name;
                } else {
                    throw new Exception('枚举值错误');
                }
                $checked = in_array($item['value'], $value) ? 'checked' : '';
                $value_str = $item['value'] ? "value=\"{$item['value']}\"" : '';
                $enum[] = "<input type=\"checkbox\" {$name_str} {$value_str}  title=\"{$item['name']}\"  lay-skin=\"primary\" {$checked}/>";
            }
            $enum = join("\n", $enum);
            $html = <<<STR
        {$enum}
STR;
        } elseif ($init_data['type'] == 'switch') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $checked = $value ? 'checked' : '';
            $html = <<<STR
            <input type="checkbox" {$name_str} lay-skin="switch" {$checked}/>
STR;
        } elseif ($init_data['type'] == 'textarea') {
            $name_placeholder = isset($init_data['placeholder']) ? $init_data['placeholder'] : "请输入内容";
            $name_str = isset($init_data['name']) ? "name=\"{$init_data['name']}\"" : '';
            $id_str = isset($init_data['name']) ? "id=\"{$init_data['name']}\"" : '';
            $html = <<<STR
            <textarea {$id_str} {$name_str} placeholder="{$name_placeholder}"  class="layui-textarea" >{$value}</textarea>
STR;
        } elseif ($init_data['type'] == 'table') {
            $value = is_array($value) ? $value : array($value);
            $init_data['init'] = isset($init_data['init']) ? $init_data['init'] : array();
            $th = array();
            foreach ($init_data['init'] as $v) {
                if (in_array($v['type'], array('hidden', 'none'))) {
                    continue;
                }
                $v['title'] = isset($v['title']) ? $v['title'] : '';
                $th[] = "<th>{$v['title']}</th>";
            }
            $th = join("\n", $th);
            $thead_tr = "<tr>{$th}</tr>";
            $tbody_tr = array();
            $i = 0;
            foreach ($value as $val) {
                $td = array();
                foreach ($init_data['init'] as $v) {
                    if ($v['type'] == 'none') {
                        continue;
                    }
                    $v['name'] = isset($v['name']) ? $v['name'] : '';
                    $_init = $v;
                    $_init['name'] = "{$init_data['name']}[{$i}][{$v['name']}]";
                    $input_html = $this->render_input($_init, isset($val[$v['name']]) ? $val[$v['name']] : '');
                    if (in_array($v['type'], array('hidden'))) {
                        $td[] = $input_html;
                    } else {
                        $td[] = "<td>{$input_html}</td>";
                    }
                }
                $td = join("\n", $td);
                $tbody_tr[] = "<tr>{$td}</tr>";
                $i++;
            }
            $tbody_tr = join("\n", $tbody_tr);
            $html = "<table class='layui-table'>
                        <thead>{$thead_tr}</thead>
                        <tbody>{$tbody_tr}</tbody>
                        <tfoot></tfoot>
                    </table>";
        } elseif ($init_data['type'] == 'file') {
            $init_data['type'] = isset($init_data['type']) ? $init_data['type'] : '';
            $init_data['name'] = isset($init_data['name']) ? $init_data['name'] : '';
            $class_str = "class=\"file\"";

            $html = array();
            $value = is_array($value) ? $value : array($value);
            foreach ($value as $ke => $val) {
                if (count($value) > 1) {
                    $name_str = "{$init_data['name']}[{$ke}]";
                } else {
                    $name_str = $init_data['name'];
                }
                $value_str = $val ? "value='{$val}'" : '';
                $html[] = "<input name='{$name_str}' {$value_str}  type='text' class='file' input_type='file'  />";
            }
            $html = join("\n", $html);
        } elseif ($init_data['type'] == 'editor') {
            $name_str = isset($init_data['name']) ? "name=\"{$init_data['name']}\"" : '';
            $id_str = isset($init_data['name']) ? "id=\"{$init_data['name']}\"" : '';
            $html = "<textarea {$id_str} {$name_str} placeholder=\"请输入内容\" input_type=\"editor\">{$value}</textarea>";
        } else {
            $init_data['type'] = isset($init_data['type']) ? $init_data['type'] : '';
            $init_data['name'] = isset($init_data['name']) ? $init_data['name'] : '';
            $init_data['name'] = is_scalar($init_data['name']) ? $init_data['name'] : json_encode($init_data['name']);
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';

            $value = is_scalar($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);

            $html = <<<STR
            <input {$name_str} value="{$value}" type="text"  class="layui-input" " />
STR;
        }
        return $html;
    }
}