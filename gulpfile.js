//http://www.cnblogs.com/EasonJim/p/6209951.html
var gulp = require('gulp');
var rev = require('gulp-rev');
var revCollector = require('gulp-rev-collector');
var clean = require('gulp-clean');
//var concat = require('gulp-concat');//多个文件合并为一个
var uglify = require('gulp-uglify');//js文件压缩
var minifyCss = require('gulp-minify-css');//压缩CSS为一行；
var runSequence = require('run-sequence');

var gulpif = require('gulp-if');
var changed = require('gulp-changed');

var postcss      = require('gulp-postcss');
var autoprefixer = require('autoprefixer');

var buildBasePath = 'dist';
var newbuildBasePath = 'newdist';


//var
gulp.task('clean', function() {
        return gulp.src([buildBasePath], {read:false})
            .pipe(clean());
});

gulp.task('cleannew', function() {
        return gulp.src([newbuildBasePath], {read:false})
            .pipe(clean());
});

gulp.task('img', function (){
    return gulp.src([
        'public/**/*.gif',
        'public/**/*.ico',
        'public/**/*.png',
        'public/**/*.jpg',
        'public/**/*.fonts',
        'public/**/*.jpeg',
        'public/**/*.woff2',
        'public/**/*.woff',
        'public/**/*.ttf',
        'public/**/*.eot',
        'public/**/*.svg',
        'public/**/*.mp3',
        'public/**/*.less',
        '!public/hsshop/**',
        '!public/qrcodes/**',
        '!public/ueditor/**',
        ])
        .pipe(changed(buildBasePath+'/res'))
        .pipe(gulp.dest(buildBasePath+'/res'))
        .pipe(gulp.dest(newbuildBasePath+'/res'));//将 rev-manifest.json 保存到 rev 目录内
});

gulp.task('css', function (){
    return gulp.src([
            'public/**/*.css',
            '!public/css/**',
            '!public/hsshop/**',
            '!public/js/**',
            '!public/qrcodes/**',
            '!public/shop/static/**',
            '!public/wechat/**',
            '!public/static/**',
            '!public/staff/static/**'
        ])
        //.pipe(postcss([ autoprefixer({ browsers: ['last 2 versions'] }) ]))
        .pipe(minifyCss())//压缩css到一样
        .pipe(rev())//文件名加MD5后缀

        .pipe(rev.manifest('css.json'))//生成一个rev-manifest.json
        .pipe(gulp.dest(buildBasePath))//将 rev-manifest.json 保存到 rev 目录内
});

gulp.task('cssF', function (){
    return gulp.src([
            'public/**/*.css',
            '!public/css/**',
            '!public/hsshop/**',
            '!public/js/**',
            '!public/qrcodes/**',
            '!public/shop/static/**',
            '!public/wechat/**',
            '!public/static/**',
            '!public/staff/static/**'
        ])
        //.pipe(postcss([ autoprefixer({ browsers: ['last 2 versions'] }) ]))
        .pipe(minifyCss())//压缩css到一样
        .pipe(rev())//文件名加MD5后缀

        .pipe(changed(buildBasePath+'/res'))
        .pipe(gulp.dest(buildBasePath+'/res'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res'));


});

gulp.task('cssStatic', function (done){
    condition = false;
    return runSequence(
        ['cssStatic1'],
        ['cssStatic2'],
        ['cssStatic3'],
        ['cssStatic4'],
        ['cssStatic5'],
        done);
});

gulp.task('js', function() {
    return gulp.src([
            'public/**/*.js',
            '!public/css/**',
            '!public/hsshop/**',
            '!public/js/**',
            '!public/qrcodes/**',
            '!public/shop/static/**',
            '!public/wechat/**',
            '!public/static/**',
            '!public/staff/static/**'
        ])
        //.pipe(uglify())//压缩js到一行
        .pipe(rev())//文件名加MD5后缀
        .pipe(rev.manifest('js.json'))////生成一个rev-manifest.json
        .pipe(gulp.dest(buildBasePath))//将 rev-manifest.json 保存到 rev 目录内

});

gulp.task('jsF', function() {
    return gulp.src([
            'public/**/*.js',
            '!public/css/**',
            '!public/hsshop/**',
            '!public/js/**',
            '!public/qrcodes/**',
            '!public/shop/static/**',
            '!public/wechat/**',
            '!public/static/**',
            '!public/staff/static/**'
        ])
        //.pipe(uglify())//压缩js到一行
        .pipe(rev())//文件名加MD5后缀
        
        .pipe(changed(buildBasePath+'/res'))
        .pipe(gulp.dest(buildBasePath+'/res'))//输出到js目录
        .pipe(gulp.dest(newbuildBasePath+'/res'));


});

gulp.task('jsStatic', function (done){
    condition = false;
    return runSequence(
        ['jsStatic1'],
        ['jsStatic2'],
        ['jsStatic3'],
        ['jsStatic4'],
        ['jsStatic5'],
        done);
});

gulp.task('html', function() {
    //html，针对js,css,img
    return gulp.src(['dist/**/*.json', 'resources/views/**/*.blade.php'])
        //.pipe(changed(buildBasePath+'/html'))
        .pipe(revCollector({replaceReved:true }))
        .pipe(gulp.dest(buildBasePath+'/html'))
        .pipe(gulp.dest(newbuildBasePath+'/html'));
});

gulp.task('default', function (done) {
    condition = false;
    runSequence(
         ['cleannew'],
         ['img'],
         ['css'],
         ['cssF'],
         ['cssStatic'],
         ['js'],
         ['jsF'],
         ['jsStatic'],
         ['html'],
        done);
});

gulp.task('reload', function (done) {
    condition = false;
    runSequence(
         ['clean'],
         ['img'],
         ['css'],
         ['cssF'],
         ['cssStatic'],
         ['js'],
         ['jsF'],
         ['jsStatic'],
         ['html'],
        done);
});

gulp.task('jsStatic1', function (){
    return gulp.src([
            'public/css/**/*.js',
        ])
        .pipe(changed(buildBasePath+'/res/css'))
        .pipe(gulp.dest(buildBasePath+'/res/css'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/css'))
});
gulp.task('jsStatic2', function (){
    return gulp.src([
            'public/js/**/*.js',
        ])
        .pipe(changed(buildBasePath+'/res/js'))
        .pipe(gulp.dest(buildBasePath+'/res/js'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/js'))
});
gulp.task('jsStatic3', function (){
    return gulp.src([
            'public/shop/static/**/*.js',
        ])
        .pipe(changed(buildBasePath+'/res/shop/static'))
        .pipe(gulp.dest(buildBasePath+'/res/shop/static'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/shop/static'))
});
gulp.task('jsStatic4', function (){
    return gulp.src([
            'public/static/**/*.js',
        ])
        .pipe(changed(buildBasePath+'/res/static'))
        .pipe(gulp.dest(buildBasePath+'/res/static'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/static'))
});
gulp.task('jsStatic5', function (){
    return gulp.src([
            'public/staff/static/**/*.js'
        ])
        .pipe(changed(buildBasePath+'/res/staff/static'))
        .pipe(gulp.dest(buildBasePath+'/res/staff/static'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/staff/static'))
});

//=====================================================================

gulp.task('cssStatic1', function (){
    return gulp.src([
            'public/css/**/*.css',
        ])
        .pipe(changed(buildBasePath+'/res/css'))
        .pipe(gulp.dest(buildBasePath+'/res/css'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/css'))
});
gulp.task('cssStatic2', function (){
    return gulp.src([
            'public/js/**/*.css',
        ])
        .pipe(changed(buildBasePath+'/res/js'))
        .pipe(gulp.dest(buildBasePath+'/res/js'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/js'))
});
gulp.task('cssStatic3', function (){
    return gulp.src([
            'public/shop/static/**/*.css',
        ])
        .pipe(changed(buildBasePath+'/res/shop/static'))
        .pipe(gulp.dest(buildBasePath+'/res/shop/static'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/shop/static'))
});
gulp.task('cssStatic4', function (){
    return gulp.src([
            'public/static/**/*.css',
        ])
        .pipe(changed(buildBasePath+'/res/static'))
        .pipe(gulp.dest(buildBasePath+'/res/static'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/static'))
});
gulp.task('cssStatic5', function (){
    return gulp.src([
            'public/staff/static/**/*.css'
        ])
        .pipe(changed(buildBasePath+'/res/staff/static'))
        .pipe(gulp.dest(buildBasePath+'/res/staff/static'))//输出到css目录
        .pipe(gulp.dest(newbuildBasePath+'/res/staff/static'))
});