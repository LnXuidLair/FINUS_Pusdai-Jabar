<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsurePegawaiAccess;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CashTransferAccessTest extends TestCase
{
    public function test_balance_report_allows_dkm_and_finance_with_get_only(): void
    {
        $route = Route::getRoutes()->getByName('pegawai.laporan-keuangan.saldo-kas-bank');
        $this->assertNotNull($route);
        $this->assertContains('pegawai.access:dkm,keuangan', $route->gatherMiddleware());
        $this->assertSame(['GET', 'HEAD'], $route->methods());
    }

    public function test_all_employee_transfer_routes_require_finance_access(): void
    {
        foreach (['index', 'store', 'destroy'] as $action) {
            $route = Route::getRoutes()->getByName('pegawai.keuangan.cash-transfers.'.$action);
            $this->assertNotNull($route);
            $this->assertContains('pegawai.access:keuangan', $route->gatherMiddleware());
            $this->assertContains('auth:pegawai', $route->gatherMiddleware());
        }
    }

    public function test_finance_can_access_but_dkm_and_general_staff_cannot(): void
    {
        foreach (['Keuangan' => 200, 'DKM' => 403, 'Umum' => 403] as $job => $expected) {
            $user = new User(['role' => User::ROLE_PEGAWAI]);
            $user->setRelation('pegawai', new Pegawai(['jabatan' => $job]));
            $request = Request::create('/pegawai/keuangan/pemindahan-kas-bank');
            $request->setUserResolver(fn () => $user);
            try {
                $response = (new EnsurePegawaiAccess)->handle($request, fn () => response('OK'), 'keuangan');
                $actual = $response->getStatusCode();
            } catch (HttpException $exception) {
                $actual = $exception->getStatusCode();
            }
            $this->assertSame($expected, $actual, $job);
        }
    }
}
