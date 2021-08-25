<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateUsersTable extends Migration
{
     public function up()
  {
      Schema::create('users', function (Blueprint $table) {
          $table->bigincrements('id');
          $table->string('username')->nullable();;
          $table->string('role')->nullable();;
          $table->string('role_id')->nullable();;
          $table->string('school_name')->nullable();;
          $table->integer('school_id')->nullable();;
          $table->string('email')->unique();
          $table->timestamp('email_verified_at')->nullable();
          $table->string('password');
          $table->rememberToken();
          $table->timestamps();
          $table->softDeletes();
      });
  }

  public function down()
  {
       Schema::dropIfExists('users');
  }
}
