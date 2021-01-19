<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CompaniesController extends Controller
{
    protected $user;

    public function __construct(Request $request)
    {
        $this->user = User::query()->firstWhere('api_token', $request->header('api-token'));
    }

    public function index(Request $request)
    {
        return response()->json(['success' => true, 'data' => $this->user->companies], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $rules = [
            'title' => 'required',
            'phone' => 'required',
            'description' => 'required'
        ];
        $customMessages = [
            'required' => 'Please fill attribute :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $this->user->companies()->create($request->only('title', 'phone', 'description'));
        return response()->json(['success' => true, 'message' => 'Company created successfully!'], 200);
    }
}
