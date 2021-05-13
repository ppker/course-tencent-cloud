{% extends 'templates/main.volt' %}

{% block content %}

    {% set title = article.id > 0 ? '编辑文章' : '写文章' %}
    {% set action_url = article.id > 0 ? url({'for':'home.article.update','id':article.id}) : url({'for':'home.article.create'}) %}
    {% set source_url_display = article.source_type == 1 ? 'display:none' : 'display:block' %}

    <div class="breadcrumb">
        <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a><cite>{{ title }}</cite></a>
        </span>
    </div>

    <form class="layui-form" method="POST" action="{{ action_url }}">
        <div class="layout-main clearfix">
            <div class="layout-content">
                <div class="writer-content wrap">
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="title" value="{{ article.title }}" placeholder="请输入标题..." lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <div id="vditor"></div>
                            <textarea name="content" class="layui-hide" id="vditor-textarea">{{ article.content }}</textarea>
                        </div>
                    </div>
                    <div class="layui-form-item center">
                        <div class="layui-input-block">
                            <button class="layui-btn kg-submit" lay-submit="true" lay-filter="go">发布文章</button>
                            <input type="hidden" name="referer" value="{{ referer }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="layout-sidebar">
                <div class="layui-card">
                    <div class="layui-card-header">基本信息</div>
                    <div class="layui-card-body">
                        <div class="writer-sidebar">
                            <div class="layui-form-item">
                                <label class="layui-form-label">标签</label>
                                <div class="layui-input-block">
                                    <div id="xm-tag-ids"></div>
                                    <input type="hidden" name="xm_tags" value='{{ xm_tags|json_encode }}'>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">来源类型</label>
                                <div class="layui-input-block">
                                    <select name="source_type" lay-filter="source_type" lay-verify="required">
                                        <option value="">请选择</option>
                                        {% for value,title in source_types %}
                                            <option value="{{ value }}" {% if article.source_type == value %}selected="selected"{% endif %}>{{ title }}</option>
                                        {% endfor %}
                                    </select>
                                </div>
                            </div>
                            <div id="source-url-block" style="{{ source_url_display }}">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">来源网址</label>
                                    <div class="layui-input-block">
                                        <input class="layui-input" type="text" name="source_url" value="{{ article.source_url }}">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">关闭评论</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="closed" value="1" title="是" {% if article.closed == 1 %}checked="checked"{% endif %}>
                                    <input type="radio" name="closed" value="0" title="否" {% if article.closed == 0 %}checked="checked"{% endif %}>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">仅我可见</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="private" value="1" title="是" {% if article.private == 1 %}checked="checked"{% endif %}>
                                    <input type="radio" name="private" value="0" title="否" {% if article.private == 0 %}checked="checked"{% endif %}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

{% endblock %}

{% block link_css %}

    {{ css_link('https://cdn.jsdelivr.net/npm/vditor/dist/index.css', false) }}

{% endblock %}

{% block include_js %}

    {{ js_include('https://cdn.jsdelivr.net/npm/vditor/dist/index.min.js', false) }}
    {{ js_include('lib/xm-select.js') }}
    {{ js_include('home/js/article.edit.js') }}
    {{ js_include('home/js/vditor.js') }}

{% endblock %}