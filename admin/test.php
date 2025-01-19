<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use App\Models\Ausbildung;
use App\Models\Note;
use App\Models\Equipment;
use App\Models\EquipmentLog;

class EmployeeController extends Controller
{
    public function show($id)
{
    $user = User::with('role', 'ausbildungen', 'equipment')->findOrFail($id);
    $roles = Role::all();
    $documents = Document::where('user_id', $id)->get();
    $ausbildungen = Ausbildung::all();
    $notes = Note::where('user_id', $id)->with('creator')->get();
    $equipment = Equipment::all(); // Fetch all equipment
    $equipmentLogs = EquipmentLog::where('user_id', $id)->with('equipment')->get(); // Fetch equipment logs

    return view('admin.member.profile', compact('user', 'roles', 'documents', 'ausbildungen', 'notes', 'equipment', 'equipmentLogs'));
} 

    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    $user->update([
        'waffenschein' => $request->waffenschein,
        'licenses' => $request->licenses,
    ]);

    $this->updateEquipment($user, $request->input('equipment', []));

    $ausbildungen = [];
    if ($request->has('ausbildungen')) {
        foreach ($request->ausbildungen as $ausbildungId => $data) {
            if (isset($data['checked'])) {
                $ausbildungen[$ausbildungId] = ['rating' => $data['rating']];
            }
        }
    }
    $user->ausbildungen()->sync($ausbildungen);

    return redirect()->route('employee.show', $id)->with('success', 'Employee updated successfully.');
}


public function updateRank(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->role_id = $request->input('role_id');
        $user->rank_last_changed_by = auth()->user()->name; // Store the name of the user who edited the role
        $user->save();

        return redirect()->route('employee.show', $id)->with('success', 'Rank updated successfully.');
    }

private function updateEquipment(User $user, array $equipmentData)
{
    $equipmentSyncData = [];
    $currentUserId = auth()->id(); // Get the ID of the authenticated user

    foreach ($equipmentData as $equipmentId => $data) {
        $equipment = Equipment::findOrFail($equipmentId);
        $quantity = $data['quantity'] ?? 0;

        if ($equipment->is_consumable) {
            // Handle consumable items
            if ($quantity > 0 && $equipment->stock >= $quantity) {
                $equipment->stock -= $quantity;
                EquipmentLog::create([
                    'user_id' => $user->id,
                    'equipment_id' => $equipment->id,
                    'quantity' => $quantity,
                    'action' => 'added',
                    'changed_by' => $currentUserId, // Add the user who made the change
                ]);
            }
        } else {
            // Handle non-consumable items
            $currentlyChecked = $user->equipment->contains($equipmentId);
            $shouldBeChecked = isset($data['checked']) && $data['checked'] == '1';

            if ($shouldBeChecked && !$currentlyChecked) {
                // Item is checked and was not previously checked
                if ($equipment->stock > 0) {
                    $equipment->stock -= 1;
                    EquipmentLog::create([
                        'user_id' => $user->id,
                        'equipment_id' => $equipment->id,
                        'quantity' => 1,
                        'action' => 'added',
                        'changed_by' => $currentUserId, // Add the user who made the change
                    ]);
                    $equipmentSyncData[$equipmentId] = ['quantity' => 1];
                }
            } elseif (!$shouldBeChecked && $currentlyChecked) {
                // Item is unchecked and was previously checked
                $equipment->stock += 1;
                EquipmentLog::create([
                    'user_id' => $user->id,
                    'equipment_id' => $equipment->id,
                    'quantity' => 1,
                    'action' => 'removed',
                    'changed_by' => $currentUserId, // Add the user who made the change
                ]);
                $user->equipment()->detach($equipmentId);
            } elseif ($shouldBeChecked && $currentlyChecked) {
                // Item is checked and was previously checked, no change needed
                $equipmentSyncData[$equipmentId] = ['quantity' => 1];
            }
        }

        // Save equipment changes
        $equipment->save();
    }

    // Sync the equipment for non-consumable items without detaching existing ones
    if (count($equipmentSyncData) > 0) {
        $user->equipment()->syncWithoutDetaching($equipmentSyncData);
    }
}
}