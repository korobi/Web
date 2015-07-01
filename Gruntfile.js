module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dist: {
                files: {
                    'web/assets/css/application.css': 'src/Korobi/WebBundle/Resources/assets/sass/application.scss'
                }
            },
            options: {
                style: 'compressed',
                sourcemap: 'none'
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.registerTask('default', ['sass']);
};
