const gulp = require('gulp');
const babel = require('gulp-babel');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const watch = require('gulp-watch');


gulp.task('app', ['app.html', 'app.css', 'app.js', 'app.images']);

gulp.task('app.html', () => {
    return gulp.src([
        'src/themes/*.php',
        'src/themes/*.html'
    ])
        .pipe(gulp.dest('public_html/themes'))
});

gulp.task('app.css', () => {
    return gulp.src('src/assets/sass/style.scss')
        .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest('public_html/assets/css'))
});

gulp.task('app.js', () => {
    return gulp.src([
        'src/assets/js/**/*.js',
        'public_html/admin/modulos/**/views/*.js'
    ])
        .pipe(babel({
            //presets: ['env'],
            minified: true,
            //comments: false
        }))
        .pipe(concat('app.min.js'))
        .pipe(gulp.dest('public_html/assets/js'))
});

gulp.task('app.images', () => {
    return gulp.src('src/assets/images/**/*.*')
        .pipe(gulp.dest('public_html/assets/images'))
});

gulp.task('monitorarMudancas', () => {
    watch('src/**/*.html', () => gulp.start('app.html'));
    watch('src/**/*.php', () => gulp.start('app.html'));
    watch('src/**/*.scss', () => gulp.start('app.css'));
    watch('src/**/*.js', () => gulp.start('app.js'));
    watch('public_html/admin/modulos/**/views/*.js', () => gulp.start('app.js'));
    watch('src/assets/images/**/*.*', () => gulp.start('app.images'));
});