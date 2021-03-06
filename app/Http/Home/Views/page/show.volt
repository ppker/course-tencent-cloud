{% extends 'templates/main.volt' %}

{% block content %}

    <div class="breadcrumb">
        <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a><cite>单页</cite></a>
            <a><cite>{{ page.title }}</cite></a>
        </span>
    </div>

    <div class="page-info wrap">
        <div class="content layui-hide" id="preview">{{ page.content }}</div>
    </div>

{% endblock %}

{% block link_css %}

    {{ css_link('https://cdn.jsdelivr.net/npm/vditor/dist/index.css', false) }}

{% endblock %}

{% block include_js %}

    {{ js_include('https://cdn.jsdelivr.net/npm/vditor/dist/method.min.js', false) }}
    {{ js_include('home/js/markdown.preview.js') }}

{% endblock %}