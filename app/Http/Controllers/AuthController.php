<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email:filter|unique:users',
            'password' => 'required',
            'phone' => 'required',
        ];
        $customMessages = [
            'required' => 'Please fill attribute :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        try {
            $hasher = app()->make('hash');
            $data = $request->all();
            $data['password'] = $hasher->make($data['password']);
            User::query()->create($data);
            return response()->json(['status' => true, 'message' => 'Registration success!'], 200);
        } catch (QueryException $ex) {
            return response()->json(['status' => false, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];
        $customMessages = [
            'required' => 'Please fill attribute :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        try {
            $login = User::query()->where('email', $request->input('email'))->first();
            if ($login && Hash::check($request->input('password'), $login->password)) {
                $api_token = sha1($login->id . time());
                $login->update(['api_token' => $api_token]);
                return response()->json([
                    'success' => true,
                    'message' => 'Success login',
                    'data' => $login,
                    'api_token' => $api_token
                ], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Email/password not found'], 401);
            }
        } catch (QueryException $ex) {
            return response()->json(['success' => false, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function generateRecoveryToken(Request $request)
    {
        $rules = [
            'email' => 'required'
        ];
        $customMessages = [
            'required' => 'Please fill attribute :attribute'
        ];
        $this->validate($request, $rules, $customMessages);

        try {
            $user = User::query()->where('email', $request->input('email'))->first();
            if ($user) {
                $remember_token = sha1($user->id . time());
                $user->update(['remember_token' => $remember_token]);
                return response()->json([
                    'success' => true,
                    'message' => 'Remember token generated successfully',
                    'remember_token' => $remember_token
                ], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Email not found'], 401);
            }
        } catch (QueryException $ex) {
            return response()->json(['success' => false, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updatePasswordByRecoveryToken(Request $request)
    {
        $rules = [
            'remember_token' => 'required',
            'password' => 'required'
        ];
        $customMessages = [
            'required' => 'Please fill attribute :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        try {
            $user = User::query()->where('remember_token', $request->input('remember_token'))->first();
            if ($user) {
                $hasher = app()->make('hash');
                $user->update(['remember_token' => null, 'password' => $hasher->make($request->get('password'))]);
                return response()->json([
                    'success' => true,
                    'message' => 'User password successfully updated',
                    'data' => $user
                ], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Remember token not found'], 401);
            }
        } catch (QueryException $ex) {
            return response()->json(['success' => false, 'message' => $ex->getMessage()], 500);
        }
    }
}
