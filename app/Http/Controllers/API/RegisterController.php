<?php
   
namespace App\Http\Controllers\API;
   
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Response;

   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token = $user->createToken('MyApp')->plainTextToken;
        return $this->sendResponse(['token' => $token, 'name' => $user->name], 'User registered successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            // Revoke all tokens for the user
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
            // Create a new token
            $token = $user->createToken('MyApp')->plainTextToken;
            return $this->sendResponse(['token' => $token, 'name' => $user->name], 'User login successfully.');
        } else {
            return $this->sendError('Unauthorized', ['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // Revoke all tokens for the user
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return $this->sendResponse([], 'User logged out successfully.', Response::HTTP_NO_CONTENT);
        } else {
            return $this->sendError('Unauthorized', ['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }
    
}