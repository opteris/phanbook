<!DOCTYPE html>
<html lang="en" class="app">
<head>
    {% set controller = this.view.getControllerName(), action =  this.view.getActionName() %}
    {% set name = this.config.application.name, publicUrl = this.config.application.publicUrl %}
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, target-densitydpi=160dpi, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="keywords" content="Phanbook, Phalcon, PHP, Stack">
    <meta name="author" content="Phanbook Team">
    <meta property="og:title" content="{{ name }}">
    <meta property="og:type"  content="website">
    <meta property="og:image" content=" {{ name }}">
    <meta property="og:url" content="{{publicUrl}}">
    <link rel="shortcut icon" href="{{ getImageSrc('favicon.png') }}">
    <title>{{  get_title(false) }} - {{name}}</title>

    {%- if canonical is defined -%}
        <link rel="canonical" href="{{ publicUrl}}/{{ canonical }}"/>
        <meta property="og:url" content="{{ publicUrl }}/{{ canonical }}">
        <meta property="og:site_name" content="Phanbook">
    {%- endif -%}

    {%- if userPosts is defined -%}
        <link rel="author" href="{{publicUrl}}/@{{userPosts.getInforUser()}}">
        <link rel="publisher" href="{{ publicUrl }}">
    {%- endif -%}

    {{ stylesheet_link('//fonts.googleapis.com/css?family=Open+Sans', false)}}
    {{ stylesheet_link('themes/discourse/app.css') }}
    {{ this.assets.outputCss() }}
    <script type="text/javascript">
        var baseUri     = '{{ this.config.application.baseUri }}';
        var controller  = '{{ controller }}';
        var action      = '{{ action }}';
        var googleAnalytic = '{{ this.config.googleAnalytic }}';
    </script>
</head>

<body class="{{controller}} {{action}}">
    {{ partial('../themes/discourse/header') }}
    <div class="m-b-md">{{ this.flashSession.output() }}</div>
    <section id ="wrapper">
        {{ content() }}
    </section>
    {{ partial('../themes/discourse/footer') }}
    {{ javascript_include('//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', false)}}
    {{ javascript_include('js/wysiwyg/converter.js')}}
    {{ javascript_include('js/wysiwyg/sanitizer.js')}}
    {{ javascript_include('js/wysiwyg/editor.js')}}
    {{ javascript_include('js/notify.js')}}
    {{ javascript_include('js/app.function.js')}}
    {{ javascript_include('js/app.ajax.js')}}
    {{ javascript_include('js/app.js')}}
    {{ javascript_include('themes/discourse/discourse.js')}}
    {{ this.assets.outputJs() }}
</body>
</html>
