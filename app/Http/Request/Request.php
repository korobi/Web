<?php

namespace Korobi\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class LogPageRequest extends FormRequest {

    public function authorize() {
        return true;
    }
}
