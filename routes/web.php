<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return '
    <h2>Login Teste</h2>
    <form method="post" action="/login">
      <input type="email" name="email" placeholder="Email" required><br><br>
      <input type="password" name="password" placeholder="Senha" required><br><br>
      <button type="submit">Entrar</button>
    </form>
    <hr><a href="/diag">Diagnóstico</a>';
});

Route::post('/login', function (Request $request) {
    Session::put('user', [
        'email' => $request->email,
        'logged_at' => now()->toDateTimeString()
    ]);
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    if (!Session::has('user')) {
        return redirect('/');
    }
    $u = Session::get('user');
    return "
    <h2>Área Protegida</h2>
    <p>Email: <b>{$u['email']}</b></p>
    <p>Login em: {$u['logged_at']}</p>
    <a href='/logout'>Sair</a> | <a href='/diag'>Diagnóstico</a>";
});

Route::get('/logout', function () {
    Session::flush();
    return redirect('/');
});

Route::get('/diag', function (Request $r) {
    $headers = [];
    foreach ($r->headers->all() as $k => $v) {
        $headers[$k] = implode('; ', $v);
    }

    return response()->make("
    <h2>Diagnóstico</h2>
    <p>HTTPS detectado: " . ($r->isSecure() ? 'Sim' : 'Não') . "</p>
    <h3>Cookies</h3><pre>" . print_r($r->cookies->all(), true) . "</pre>
    <h3>Sessão</h3><pre>" . print_r(session()->all(), true) . "</pre>
    <h3>Headers</h3><pre>" . print_r($headers, true) . "</pre>
    <a href='/'>Voltar</a>", 200, ['Content-Type' => 'text/html']);
});
