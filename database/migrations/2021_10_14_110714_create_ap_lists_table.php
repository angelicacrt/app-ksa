<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        // \database/migrations/2021_10_14_110714_create_ap_lists_table.php
        Schema::create('ap_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('cabang');

            $table->string('creationTime', 15);
            $table->string('status', 15)->default('Open');
            $table->integer('tracker')->default(5);
            $table->decimal('paidPrice', 13, 2)->default(0);

            // File Partial 1
            $table->string('doc_partial1')->nullable();
            $table->string('status_partial1', 15)->nullable();
            $table->string('uploadTime_partial1', 15)->nullable();
            $table->string('description_partial1', 180)->nullable();  
            $table->string('path_to_file1', 15)->nullable();
            $table->string('s3_url_to_file1')->nullable();

            // File Partial 2
            $table->string('doc_partial2')->nullable();
            $table->string('status_partial2', 15)->nullable();
            $table->string('uploadTime_partial2', 15)->nullable();
            $table->string('description_partial2', 180)->nullable();  
            $table->string('path_to_file2', 15)->nullable();
            $table->string('s3_url_to_file2')->nullable();

            // File Partial 3
            $table->string('doc_partial3')->nullable();
            $table->string('status_partial3', 15)->nullable();
            $table->string('uploadTime_partial3', 15)->nullable();
            $table->string('description_partial3', 180)->nullable();  
            $table->string('path_to_file3', 15)->nullable();
            $table->string('s3_url_to_file3')->nullable();

            // File Partial 4
            $table->string('doc_partial4')->nullable();
            $table->string('status_partial4', 15)->nullable();
            $table->string('uploadTime_partial4', 15)->nullable();
            $table->string('description_partial4', 180)->nullable();  
            $table->string('path_to_file4', 15)->nullable();
            $table->string('s3_url_to_file4')->nullable();

            // File Partial 5
            $table->string('doc_partial5')->nullable();
            $table->string('status_partial5', 15)->nullable();
            $table->string('uploadTime_partial5', 15)->nullable();
            $table->string('description_partial5', 180)->nullable();  
            $table->string('path_to_file5', 15)->nullable();
            $table->string('s3_url_to_file5')->nullable();

            // File Partial 6
            $table->string('doc_partial6')->nullable();
            $table->string('status_partial6', 15)->nullable();
            $table->string('uploadTime_partial6', 15)->nullable();
            $table->string('description_partial6', 180)->nullable();  
            $table->string('path_to_file6', 15)->nullable();
            $table->string('s3_url_to_file6')->nullable();

            // File Partial 7
            $table->string('doc_partial7')->nullable();
            $table->string('status_partial7', 15)->nullable();
            $table->string('uploadTime_partial7', 15)->nullable();
            $table->string('description_partial7', 180)->nullable();  
            $table->string('path_to_file7', 15)->nullable();
            $table->string('s3_url_to_file7')->nullable();

            // File Partial 8
            $table->string('doc_partial8')->nullable();
            $table->string('status_partial8', 15)->nullable();
            $table->string('uploadTime_partial8', 15)->nullable();
            $table->string('description_partial8', 180)->nullable();  
            $table->string('path_to_file8', 15)->nullable();
            $table->string('s3_url_to_file8')->nullable();

            // File Partial 9
            $table->string('doc_partial9')->nullable();
            $table->string('status_partial9', 15)->nullable();
            $table->string('uploadTime_partial9', 15)->nullable();
            $table->string('description_partial9', 180)->nullable();  
            $table->string('path_to_file9', 15)->nullable();
            $table->string('s3_url_to_file9')->nullable();

            // File Partial 10
            $table->string('doc_partial10')->nullable();
            $table->string('status_partial10', 15)->nullable();
            $table->string('uploadTime_partial10', 15)->nullable();
            $table->string('description_partial10', 180)->nullable();  
            $table->string('path_to_file10', 15)->nullable();
            $table->string('s3_url_to_file10')->nullable();

            // File Partial 11
            $table->string('doc_partial11')->nullable();
            $table->string('status_partial11', 15)->nullable();
            $table->string('uploadTime_partial11', 15)->nullable();
            $table->string('description_partial11', 180)->nullable();  
            $table->string('path_to_file11', 15)->nullable();
            $table->string('s3_url_to_file11')->nullable();

            // File Partial 12
            $table->string('doc_partial12')->nullable();
            $table->string('status_partial12', 15)->nullable();
            $table->string('uploadTime_partial12', 15)->nullable();
            $table->string('description_partial12', 180)->nullable();  
            $table->string('path_to_file12', 15)->nullable();
            $table->string('s3_url_to_file12')->nullable();

            // File Partial 13
            $table->string('doc_partial13')->nullable();
            $table->string('status_partial13', 15)->nullable();
            $table->string('uploadTime_partial13', 15)->nullable();
            $table->string('description_partial13', 180)->nullable();  
            $table->string('path_to_file13', 15)->nullable();
            $table->string('s3_url_to_file13')->nullable();

            // File Partial 14
            $table->string('doc_partial14')->nullable();
            $table->string('status_partial14', 15)->nullable();
            $table->string('uploadTime_partial14', 15)->nullable();
            $table->string('description_partial14', 180)->nullable();  
            $table->string('path_to_file14', 15)->nullable();
            $table->string('s3_url_to_file14')->nullable();

            // File Partial 15
            $table->string('doc_partial15')->nullable();
            $table->string('status_partial15', 15)->nullable();
            $table->string('uploadTime_partial15', 15)->nullable();
            $table->string('description_partial15', 180)->nullable();  
            $table->string('path_to_file15', 15)->nullable();
            $table->string('s3_url_to_file15')->nullable();

            // File Partial 16
            $table->string('doc_partial16')->nullable();
            $table->string('status_partial16', 15)->nullable();
            $table->string('uploadTime_partial16', 15)->nullable();
            $table->string('description_partial16', 180)->nullable();  
            $table->string('path_to_file16', 15)->nullable();
            $table->string('s3_url_to_file16')->nullable();

            // File Partial 17
            $table->string('doc_partial17')->nullable();
            $table->string('status_partial17', 15)->nullable();
            $table->string('uploadTime_partial17', 15)->nullable();
            $table->string('description_partial17', 180)->nullable();  
            $table->string('path_to_file17', 15)->nullable();
            $table->string('s3_url_to_file17')->nullable();

            // File Partial 18
            $table->string('doc_partial18')->nullable();
            $table->string('status_partial18', 15)->nullable();
            $table->string('uploadTime_partial18', 15)->nullable();
            $table->string('description_partial18', 180)->nullable();  
            $table->string('path_to_file18', 15)->nullable();
            $table->string('s3_url_to_file18')->nullable();

            // File Partial 19
            $table->string('doc_partial19')->nullable();
            $table->string('status_partial19', 15)->nullable();
            $table->string('uploadTime_partial19', 15)->nullable();
            $table->string('description_partial19', 180)->nullable();  
            $table->string('path_to_file19', 15)->nullable();
            $table->string('s3_url_to_file19')->nullable();

            // File Partial 20
            $table->string('doc_partial20')->nullable();
            $table->string('status_partial20', 15)->nullable();
            $table->string('uploadTime_partial20', 15)->nullable();
            $table->string('description_partial20', 180)->nullable();  
            $table->string('path_to_file20', 15)->nullable();
            $table->string('s3_url_to_file20')->nullable();
            
            $table->foreign('order_id')->references('id')->on('order_heads')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ap_lists');
    }
}
