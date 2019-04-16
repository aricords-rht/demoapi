<?php

namespace App\Http\Controllers;

use App\TaskType;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index()
    {
        $pageTitle = 'API Documentation';
        $taskTypes = TaskType::all();

        return view('documentation.index', compact('pageTitle', 'taskTypes'));
    }
}
