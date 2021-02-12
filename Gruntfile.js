module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        uglify: {
            jq: {
                src: ["Assets/js/jQuery/*.js"],
                dest: "Assets/js/jq.min.js"
            },
            jqp: {
                src: ["Assets/js/jQuery/plugins/*.js"],
                dest: "Assets/js/jqp.min.js"
            },
            written: {
                options: {
                    beautify: true,
                    mangle: false,
                    compress: false,
                },
                src: [
                    "Assets/js/custom/*.js",
                    "Assets/js/custom/**/*.js"
                ],
                dest: "Assets/js/written.min.js"
            },
            sw: {
                src: [
                    "Assets/js/custom/*.jsw",
                    "Assets/js/custom/**/*.jsw"
                ],
                dest: "Assets/js/sw.min.js"
            },
            third: {
                src: ["Assets/js/thirdParty/*.js"],
                dest: "Assets/js/thirdparty.min.js"
            }
        },
        sass: {
            dist: {
                files: [
                    {
                        "Assets/css/written.new.min.css": "Assets/css/sass/include.scss"
                    },
                    {
                        expand: true,
                        cwd: "Assets/css/thirdparty",
                        src: ["*.scss"],
                        dest: "Assets/css/thirdparty",
                        ext: ".css"
                    }
                ]


            }
        },
        cssmin: {
            target: {
                files: {
                    "Assets/css/libs.min.css": [
                        "Assets/css/thirdparty/*.css"
                    ]
                }
            }
        },
        cachebuster: {
            options: {
                format: "php"
            },
            "Core/cachebuster.php": [
                "Assets/css/imports.min.css",
                "Assets/css/libs.min.css",
                "Assets/css/written.new.min.css",
                "Assets/js/written.min.js",
                "Assets/js/jq.min.js",
                "Assets/js/jqp.min.js",
                "Assets/js/thirdparty.min.js"
            ]
        },
        watch: {
            main: {
                files: [
                    "Assets/js/*/*.js",
                    "Assets/js/*/*.jsw",
                    "Assets/js/*/*/*.js",
                    "Assets/js/*/*/*.jsw",
                    "Assets/css/*/*.scss",
                    "Assets/css/*/*/*.scss",
                    "Assets/css/*/*/*/*.scss",
                ],
                tasks: ["Deployed"]
            },
            cssonly: {
                files: [
                    "Assets/css/*/*.scss",
                    "Assets/css/*/*/*.scss",
                    "Assets/css/*/*/*/*.scss",
                ],
                tasks: ["DevCSSOnly"]
            },
            jsonly: {
                files: [
                    "Assets/js/*/*.js",
                    "Assets/js/*/*.jsw",
                    "Assets/js/*/*/*.js",
                    "Assets/js/*/*/*.jsw",
                ],
                tasks: ["DevJSOnly"]
            }
        },
    });

    grunt.loadNpmTasks("grunt-contrib-uglify-es");
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-cachebuster");
    grunt.loadNpmTasks("grunt-contrib-cssmin");
    grunt.loadNpmTasks("grunt-sass-globbing");


    grunt.registerTask("Deployed", ["uglify", "sass", "cssmin", "cachebuster"]);

    grunt.registerTask("DevCSSOnly", ["sass", "cssmin", "cachebuster", "watch:cssonly"]);
    grunt.registerTask("DevJSOnly", ["uglify", "cachebuster", "watch:jsonly"]);

    grunt.registerTask("Live", ["Deployed"]);
    grunt.registerTask("Staging", ["Live"]);
    grunt.registerTask("Dev", ["Live", "watch:main"]);
    grunt.registerTask("default", ["Live"]);


};
