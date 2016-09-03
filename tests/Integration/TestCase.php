<?php

namespace Padosoft\Laravel\Support\Test\Integration;

use File;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Padosoft\Test\traits\ExceptionTestable;
use Padosoft\Test\traits\FileSystemTestable;

abstract class TestCase extends Orchestra
{
    use ExceptionTestable, FileSystemTestable;

    /**
     * @var \Padosoft\Laravel\Support\Test\Integration\User
     */
    protected $testUser;

    /**
     * @var \Padosoft\Uploadable\Test\Integration\TestModel
     */
    protected $testModel;

    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->testUser = User::first();
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory($this->getTempDirectory());

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            //'database' => $this->getTempDirectory().'/database.sqlite',
            'prefix' => '',
        ]);

        //$app['config']->set('view.paths', [__DIR__.'/resources/views']);
    }

    /**
     * @param  $app
     */
    protected function setUpDatabase(Application $app)
    {
     //   file_put_contents($this->getTempDirectory().'/database.sqlite', null);

        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('remember_token')->nullable();
        });
        if(!User::first()) {
            User::create(['email' => 'test@user.com']);
        }
    }

    protected function initializeDirectory(string $directory)
    {
        return;
        /*
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory);
        */
    }

    public function getTempDirectory() : string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'temp';
    }

    /**
     * Refresh the testuser.
     */
    public function refreshTestUser()
    {
        $this->testUser = User::find($this->testUser->id);
    }
}
