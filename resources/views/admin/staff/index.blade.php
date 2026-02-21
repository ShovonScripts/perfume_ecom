@extends('admin.layouts.app')
@section('title', 'Staff & Role Management')

@section('content')
<div class="space-y-6">

    <!-- Permissions Matrix -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">📋 Permissions Matrix</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Feature</th>
                        <th class="py-2 px-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Super Admin</th>
                        <th class="py-2 px-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Manager</th>
                        <th class="py-2 px-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Staff</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    @foreach([
                        ['Dashboard', true, true, true],
                        ['Orders (View)', true, true, true],
                        ['Products / Brands / Categories', true, true, false],
                        ['Orders (Update / Bulk)', true, true, false],
                        ['Coupons / Customers', true, true, false],
                        ['Inventory / Settings', true, false, false],
                        ['Staff Management', true, false, false],
                    ] as [$feature, $sa, $mgr, $stf])
                    <tr>
                        <td class="py-2 px-3 text-gray-700 dark:text-gray-300">{{ $feature }}</td>
                        <td class="py-2 px-3 text-center">{{ $sa ? '✅' : '❌' }}</td>
                        <td class="py-2 px-3 text-center">{{ $mgr ? '✅' : '❌' }}</td>
                        <td class="py-2 px-3 text-center">{{ $stf ? '✅' : '❌' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Admin Staff ({{ $staff->count() }})
            </h3>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">Only Super Admins can modify roles & create</span>
                <a href="{{ route('admin.staff.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    + Add New Staff
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">#</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Name</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Current Role</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Change Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $index => $member)
                    <tr class="border-b dark:border-gray-700 hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition {{ $member->id === auth()->id() ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}">
                        <td class="py-3 px-4 text-gray-400 dark:text-gray-500 text-xs">{{ $index + 1 }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $member->name }}
                                        @if($member->id === auth()->id())
                                            <span class="text-xs text-indigo-500 font-normal">(You)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Joined {{ $member->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $member->email }}</td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $roleColors = [
                                    'super_admin' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                    'manager' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                    'staff' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                ];
                                $roleIcons = ['super_admin' => '👑', 'manager' => '📋', 'staff' => '👤'];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $roleColors[$member->role] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $roleIcons[$member->role] ?? '👤' }} {{ \App\Models\User::ROLES[$member->role] ?? ucfirst($member->role) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($member->id === auth()->id())
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Cannot change own role</span>
                            @else
                                <form method="POST" action="{{ route('admin.staff.updateRole', $member) }}" class="inline-flex items-center gap-2">
                                    @csrf
                                    <select name="role" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-1.5 focus:ring-2 focus:ring-purple-500">
                                        @foreach(\App\Models\User::ROLES as $value => $label)
                                            <option value="{{ $value }}" {{ $member->role === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            onclick="return confirm('Change {{ $member->name }}\'s role?')"
                                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-1.5 rounded-lg text-xs font-medium transition">
                                        Update
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
