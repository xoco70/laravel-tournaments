<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Xoco70\LaravelTournaments\DBHelpers;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;

class CreateChampionshipSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('championship_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias')->nullable();
            $table->integer('championship_id')->unsigned()->unique();
            $table->foreign('championship_id')
                ->references('id')
                ->onUpdate('cascade')
                ->on('championship')
                ->onDelete('cascade');

            // Category Section
            $table->tinyInteger('treeType')->default(ChampionshipSettings::SINGLE_ELIMINATION);
            $table->tinyInteger('fightingAreas')->unsigned()->nullable()->default(1);
            $table->integer('limitByEntity')->unsigned()->nullable();

            // Preliminary
            $table->boolean('hasPreliminary')->default(1);
            $table->boolean('preliminaryGroupSize')->default(3);
            $table->tinyInteger('preliminaryWinner')->default(1); // Number of Competitors that go to next level
            $table->text('preliminaryDuration')->nullable(); // Match Duration in preliminary heat

            // Team
            $table->tinyInteger('teamSize')->nullable(); // Default is null
            $table->tinyInteger('teamReserve')->nullable(); // Default is null

            // Seed
            $table->smallInteger('seedQuantity')->nullable(); // Competitors seeded in tree

            //TODO This should go in another table that is not for tree construction but for rules
            // Rules
            $table->boolean('hasEncho')->default(1);
            $table->tinyInteger('enchoQty')->default(0);
            $table->text('enchoDuration')->nullable();
            $table->boolean('hasHantei')->default(false);
            $table->smallInteger('cost')->nullable(); // Cost of competition

            $table->text('fightDuration')->nullable(); // Can't apply default because text
            $table->smallInteger('hanteiLimit')->default(0); // 0 = none, 1 = 1/8, 2 = 1/4, 3=1/2, 4 = FINAL
            $table->smallInteger('enchoGoldPoint')->default(0); // 0 = none, 1 = 1/8, 2 = 1/4, 3=1/2, 4 = FINAL

            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DBHelpers::setFKCheckOff();
        Schema::dropIfExists('championship_settings');
        DBHelpers::setFKCheckOn();
    }
}
