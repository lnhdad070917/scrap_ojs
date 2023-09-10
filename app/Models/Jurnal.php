<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    protected $table = 'jurnal';

    protected $fillable = ['link_id', 'judul', 'author', 'doi', 'pdf_link', 'web_link', 'pages', 'link_id'];
}