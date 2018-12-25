@servers(['web' => ['forge@kendozone.com']])

@task('deploy', ['on' => 'web'])
    cd my.kendozone.com
    git pull origin master
    composer install

    cd ~
    cd demo.kendozone.com
    git pull origin master
    composer install
@endtask