<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('announcements', function (Blueprint $table) {
    $table->string('link_title')->nullable();
});
