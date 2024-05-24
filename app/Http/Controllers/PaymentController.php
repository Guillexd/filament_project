<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LaborDay;
use App\Models\Payment;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function __invoke()
    {
        $lowerclasses = User::all();
        foreach ($lowerclasses as $lowclass) {
            $this->calculateSalary($lowclass->id, $lowclass->idProfession);
        }
        return back();
    }

    protected function calculateSalary($lowclassId, $lowclassProfessionId)
    {
        $mesactual = date("m");
        $mesactualFilter = date("Y-m");
        $diasLaborables = LaborDay::find($mesactual)->quantity;
        $asistencias = Attendance::where('idUser', $lowclassId)->where('created_at', 'like', "%{$mesactualFilter}%")->count();
        $profession = Profession::find($lowclassProfessionId);
        $payment = new Payment();
        $payment->idUser = $lowclassId;
        $payment->bonus = (($asistencias/$diasLaborables) * 100) >= 95 ? $profession->bonus : 0;
        $payment->discount = (($asistencias/$diasLaborables) * 100) < 51 ? $profession->salary : ((($asistencias/$diasLaborables) * 100) < 95 ? $profession->bonus : 0);
        $payment->salary = $profession->salary + $payment->bonus - $payment->discount;
        $payment->state = 0;
        $payment->save();
    }

    public function generatePDF (Request $request, Payment $payment) {
        $user = User::with('profession')->whereId($request->query('user'))->first();
        $pdf = PDF::loadView('payment', compact('user', 'payment'));
        return $pdf->stream("pago_$user->name.pdf");
    }

    public function destroy()
    {
        $mesactualFilter = date("Y-m");
        Payment::where('created_at', 'like', "%{$mesactualFilter}%")->delete();
        return back();
    }
}
