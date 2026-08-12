<?php

/**
 * Class BootstrapForm
 * Bootstrap 5 风格表单渲染器（替代 layui）
 * @property Form $form_instance
 */
class BootstrapForm
{
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
        $form_class = $formObj->config['form_class'] ? 'class="' . join(' ', array_merge(array('form-horizontal'), $formObj->config['form_class'])) . '"' : '';
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
        $tip_html = $description ? '<div class="form-text">' . $description . '</div>' : '';
        $input_html = $this->render_input($init_data, isset($init_data['value']) ? $init_data['value'] : '');
        if (strtolower($input_type) == 'hidden') {
            $block_html = <<<ST
            {$input_html}
ST;
        } elseif (strtolower($input_type) == 'none') {
            $block_html = '';
        } elseif (strtolower($input_type) == 'submit') {
            $block_html = <<<ST
                <div class="mb-3 row">
                    <div class="col-sm-12">
                     {$input_html}
                     </div>
                </div>
ST;
        } else {
            $label_text = isset($init_data['title']) ? $init_data['title'] : '';
            $block_html = <<<ST
                <div class="mb-3 row">
                    <label class="col-sm-1 col-form-label">{$label_text}</label>
                    <div class="col-sm-9">
                        {$input_html}
                        {$tip_html}
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
                $html = $input_html;
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
                  <div class="col-auto col-form-label {$display_none_class_str}" {$display_none_css_str}>{$label_text}</div>
                  <div class="col-auto" style="width: 100px;">
                      {$input_html1}
                  </div>
                  <div class="col-auto col-form-label text-center">-</div>
                  <div class="col-auto" style="width: 100px;">
                      {$input_html2}
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
                    <div class="col-auto col-form-label {$display_none_class_str}" {$display_none_css_str}>{$label_text}</div>
                    <div class="col-auto">
                        {$input_html}
                    </div>
str;
            }
            $inline_html[] = $html;
        }
        $inline_html = join(PHP_EOL, $inline_html);
        $block_html = <<<ST
            <div class="mb-3 row align-items-center">
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
    <button type="reset" class="btn btn-default" {$init_data['reset_btn_raw_text']} >重置</button>
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
            <button type="submit" class="btn btn-primary btn-block" {$init_data['raw_text']} >{$init_data['title']}</button>
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
                    $name_placeholder = isset($init_data['placeholder'][$ke]) ? "{$init_data['placeholder'][$ke]}" : "";
                } else {
                    $name_str = $init_data['name'];
                    $name_placeholder = isset($init_data['placeholder']) ? $init_data['placeholder'] : "";
                }
                $html[] = <<<str
<input name="{$name_str}" value="{$val}" type="text"  placeholder="{$name_placeholder}"   class="form-control"  {$disabled_str}/>
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
<input {$name_str}  class="form-control"  {$disabled_str} value="{$val}" type="text"  input_type="date"/>
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
            <input {$name_str} value="{$value}" type="password"  class="form-control"  />
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
            <select class="form-select" {$name_str} {$disabled_str}>
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
            <select class="form-select" {$name_str} {$disabled_str} multiple>
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
                $id_str = "id=\"{$init_data['name']}_{$key}\"";
                $enum[] = '<div class="form-check form-check-inline">'
                    . '<input class="form-check-input" type="radio" ' . $name_str . ' value="' . $item['value'] . '" ' . $id_str . ' ' . $checked . '/>'
                    . '<label class="form-check-label" for="' . $init_data['name'] . '_' . $key . '">' . $item['name'] . '</label>'
                    . '</div>';
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
                $id_str = "id=\"{$init_data['name']}_{$key}\"";
                $enum[] = '<div class="form-check form-check-inline">'
                    . '<input class="form-check-input" type="checkbox" ' . $name_str . ' ' . $value_str . ' ' . $id_str . ' ' . $checked . '/>'
                    . '<label class="form-check-label" for="' . $init_data['name'] . '_' . $key . '">' . $item['name'] . '</label>'
                    . '</div>';
            }
            $enum = join("\n", $enum);
            $html = <<<STR
        {$enum}
STR;
        } elseif ($init_data['type'] == 'switch') {
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';
            $checked = $value ? 'checked' : '';
            $html = <<<STR
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" {$name_str} {$checked}/>
            </div>
STR;
        } elseif ($init_data['type'] == 'textarea') {
            $name_placeholder = isset($init_data['placeholder']) ? $init_data['placeholder'] : "请输入内容";
            $name_str = isset($init_data['name']) ? "name=\"{$init_data['name']}\"" : '';
            $id_str = isset($init_data['name']) ? "id=\"{$init_data['name']}\"" : '';
            $html = <<<STR
            <textarea {$id_str} {$name_str} placeholder="{$name_placeholder}"  class="form-control" >{$value}</textarea>
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
            $html = "<table class='table table-bordered'>
                        <thead>{$thead_tr}</thead>
                        <tbody>{$tbody_tr}</tbody>
                        <tfoot></tfoot>
                    </table>";
        } elseif ($init_data['type'] == 'file') {
            $init_data['type'] = isset($init_data['type']) ? $init_data['type'] : '';
            $init_data['name'] = isset($init_data['name']) ? $init_data['name'] : '';
            $class_str = "class=\"file form-control\"";

            $html = array();
            $value = is_array($value) ? $value : array($value);
            foreach ($value as $ke => $val) {
                if (count($value) > 1) {
                    $name_str = "{$init_data['name']}[{$ke}]";
                } else {
                    $name_str = $init_data['name'];
                }
                $value_str = $val ? "value='{$val}'" : '';
                $html[] = "<input name='{$name_str}' {$value_str}  type='text' class='file form-control' input_type='file'  />";
            }
            $html = join("\n", $html);
        } elseif ($init_data['type'] == 'editor') {
            $name_str = isset($init_data['name']) ? "name=\"{$init_data['name']}\"" : '';
            $id_str = isset($init_data['name']) ? "id=\"{$init_data['name']}\"" : '';
            $html = "<textarea {$id_str} {$name_str} placeholder=\"请输入内容\" input_type=\"editor\" class=\"form-control\">{$value}</textarea>";
        } else {
            $init_data['type'] = isset($init_data['type']) ? $init_data['type'] : '';
            $init_data['name'] = isset($init_data['name']) ? $init_data['name'] : '';
            $init_data['name'] = is_scalar($init_data['name']) ? $init_data['name'] : json_encode($init_data['name']);
            $name_str = $init_data['name'] ? "name=\"{$init_data['name']}\"" : '';

            $value = is_scalar($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);

            $html = <<<STR
            <input {$name_str} value="{$value}" type="text"  class="form-control" " />
STR;
        }
        return $html;
    }
}