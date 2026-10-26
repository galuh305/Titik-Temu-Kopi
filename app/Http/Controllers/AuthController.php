<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller {
 public function show(){ return view('auth.login'); }
 public function login(Request $request){
  $data=$request->validate(['email'=>'required|email','password'=>'required|string']);
  $key=Str::lower($data['email']).'|'.$request->ip();
  if(RateLimiter::tooManyAttempts($key,5)) throw ValidationException::withMessages(['email'=>'Terlalu banyak percobaan. Coba lagi sebentar.']);
  if(!Auth::attempt(['email'=>$data['email'],'password'=>$data['password'],'status'=>1],$request->boolean('remember'))){
   RateLimiter::hit($key,60); throw ValidationException::withMessages(['email'=>'Email atau password salah, atau akun tidak aktif.']);
  }
  RateLimiter::clear($key); $request->session()->regenerate();
  return redirect()->intended(route('dashboard'));
 }
 public function logout(Request $request){ Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('login');}
}
