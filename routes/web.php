<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentosController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PrivilegioController;
use App\Http\Controllers\RolPrivilegioController;
use App\Http\Controllers\GerenciaController;
use App\Http\Controllers\SubgerenciaController;
use App\Http\Controllers\SubUsuarioController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackupAccionesController;








/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Rutas de autenticación

// Route::get('/', function () {
//     return redirect()->route('publics');
// });

Route::get('/', [PublicController::class, 'index'])->name('public.index');

// Route::post('/login', function () {
//     return view('auth.login');
// });

Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rutas de cambio de contraseña
Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->middleware('auth')->name('change-password');
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth');

// Rutas de registro (solo para administradores)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->middleware('auth')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('auth');

Route::resource('documentos', DocumentosController::class);
Route::get('/documentos/{id}', [DocumentosController::class, 'show'])->name('documentos.show');


Route::middleware(['auth'])->group(function () {
    // Otras rutas protegidas por autenticación...

    // Ruta a la vista documentos.index
    // Route::get('/documentos', [DocumentosController::class, 'index'])->name('documentos.index');
    // Route::get('/documentos/create', [DocumentosController::class, 'create'])->name('documentos.create');
    // Route::get('/documentos/edit', [DocumentosController::class, 'edit'])->name('documentos.edit');
    // Route::post('documentos', [DocumentosController::class, 'store'])->name('documentos.store');

    Route::resource('documentos', DocumentosController::class)->except(['index', 'show']);

    // Route::resource('subgerencias', SubgerenciaController::class);
    // Route::prefix('gerencias/{gerencia}')->group(function () {
    //     Route::resource('subgerencias', SubgerenciaController::class);
    // });


    // route::get('/gerencias/{gerencia}', [GerenciaController::class, 'show'])->name('gerencias.show');
    // route::get('/gerencias/{id}', [GerenciaController::class, 'mostrarGerencia'])->name('gerencias.show');

    // Register Usuarios

    // Grupo de rutas accesibles solo con el privilegio "Acceso Total"
    Route::group(['middleware' => ['auth', 'privilege:Acceso Total']], function () {
        Route::resource('privilegios', PrivilegioController::class);
        Route::resource('tipodocumento', TipoDocumentoController::class);
        // Route::resource('dashboard', DashboardController::class);
        // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::resource('rolprivilegios', RolPrivilegioController::class);

        // Nuevas rutas para el historial de acciones
        Route::get('/backup-acciones', [BackupAccionesController::class, 'index'])
            ->name('backup-acciones.index');
        Route::get('/backup-acciones/{backupAccion}', [BackupAccionesController::class, 'show'])
            ->name('backup-acciones.show');
    });

    // Rutas compartidas entre "Acceso Total" y "Acceso a Gerencia"
    Route::group(['middleware' => ['auth', 'privilege:Acceso Total,Acceso a Gerencia']], function () {
        // Rutas de usuarios con registro de acciones (solo modificaciones)
        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])
            ->middleware(['register.actions'])
            ->name('usuarios.update');

        Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])
            ->middleware(['register.actions'])
            ->name('usuarios.destroy');

        Route::put('/usuarios/{usuario}/actualizar-contrasena', [UserController::class, 'actualizarContrasena'])
            ->middleware(['register.actions'])
            ->name('usuarios.actualizarContrasena');

        // Rutas sin registro de acciones (creación y lectura)
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
        Route::get('/usuarios/{usuario}', [UserController::class, 'show'])->name('usuarios.show');
        Route::get('/usuarios/{usuario}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::get('/usuarios/{usuario}/cambiar-contrasena', [UserController::class, 'cambiarContrasena'])
            ->name('usuarios.cambiarContrasena');

        // Todo el resto de tus rutas sin modificar
        Route::resource('personas', PersonaController::class);
        Route::resource('roles', RolController::class);

        Route::get('/subusuarios/{id}/cambiar-contrasena', [SubusuarioController::class, 'cambiarContrasena'])
            ->middleware('auth')
            ->name('subusuarios.cambiarContrasena');

        Route::put('/subusuarios/{id}/actualizar-contrasena', [SubusuarioController::class, 'actualizarContrasena'])
            ->middleware('auth')
            ->name('subusuarios.actualizarContrasena');

        Route::get('/register', [UserController::class, 'create'])
            ->middleware('auth')
            ->name('register.create');

        Route::post('/register', [UserController::class, 'store'])
            ->middleware('auth')
            ->name('register.store');

        Route::prefix('gerencias/{gerencia}')->group(function () {
            Route::resource('subgerencias', SubgerenciaController::class);
            Route::resource('subusuarios', SubUsuarioController::class);
        });

        Route::resource('gerencias', GerenciaController::class)
            ->middleware('auth');
        route::get('/gerencias/{id}', [GerenciaController::class, 'show'])
            ->middleware(['auth', 'checkGerenciaOwnership']);

        Route::get('/documentos/{documentoId}/historial', [DocumentosController::class, 'mostrarHistorial'])
            ->name('documentos.historial')
            ->where('documentoId', '0');
    });

    Route::get('/reporte-documentos', [DocumentosController::class, 'generarReporte'])->name('reporte.documentos');


    Route::post('/documentos/{id}/cambiarEstado', [DocumentosController::class, 'cambiarEstado'])->name('documentos.cambiarEstado');

    Route::get('/historial/exportar', [DocumentosController::class, 'exportarPDF'])->name('historial.exportar');
});
