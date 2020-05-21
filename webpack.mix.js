const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css').version();//加上 “.version”避免浏览器缓存静态页面造成修改数据无改变的情况
