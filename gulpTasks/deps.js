const gulp = require('gulp');
const concat = require('gulp-concat');

gulp.task('deps', ['deps.css', 'deps.fonts', 'deps.js']);

gulp.task('deps.css', () => {
    return gulp.src([
        'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'node_modules//owl.carousel/dist/assets/owl.carousel.min.css',
        // 'node_modules/aos/dist/aos.css',
        'node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css',
    ])
        .pipe(concat('deps.min.css'))
        .pipe(gulp.dest('public_html/assets/css'))
});

gulp.task('deps.fonts', () => {
    return gulp.src([
        'node_modules/@fortawesome/fontawesome-free/webfonts/*.*',
        'src/assets/sass/fonts/webfonts/*.*'
    ])
        .pipe(gulp.dest('public_html/assets/webfonts'))
});
gulp.task('deps.js', () => {
    return gulp.src([
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/popper.js/dist/umd/popper.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        // 'node_modules/ekko-lightbox/dist/ekko-lightbox.min.js',
        'node_modules/owl.carousel/dist/owl.carousel.min.js',
        'node_modules/jquery-mask-plugin/dist/jquery.mask.min.js',
        // 'node_modules/wow.js/dist/wow.min.js',
        // 'node_modules/aos/dist/aos.js',
        'node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js',
        'src/assets/js/vendor/*.js'

    ])
        .pipe(concat('deps.min.js'))
        .pipe(gulp.dest('public_html/assets/js'))
});