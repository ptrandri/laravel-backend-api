<?php
   
namespace App\Http\Controllers\API;
   
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
   
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
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 

            // revoke token
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
            // create the token
            $token = $user->createToken('MyApp')->plainTextToken;
            $success['token'] = $token;
            $success['name'] = $user->name;
            
   
            return $this->sendResponse($success, 'User login successfully.');
            }else{
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
            }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return $this->sendResponse([], 'User logged out successfully.');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
        }
    }
    
}