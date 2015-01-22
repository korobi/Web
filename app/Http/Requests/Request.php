<?php namespace Yukai\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogPageRequest extends FormRequest {

    public function authorize() {
        return true;
    }



}
