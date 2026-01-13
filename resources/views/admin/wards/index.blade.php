@extends('layouts.admin')

@section('title', 'Wards')
@section('page-title', 'Wards Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-medium text-gray-900">All Wards</h3>
        <p class="text-sm text-gray-600">Manage hospital inpatient wards</p>
    </div>
    <a href="{{ route('admin.wards.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Add Ward
    </a>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.wards.index') }}" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <select name="class" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Classes</option>
            <option value="VIP" {{ request('class') === 'VIP' ? 'selected' : '' }}>VIP</option>
            <option value="Class 1" {{ request('class') === 'Class 1' ? 'selected' : '' }}>Class 1</option>
            <option value="Class 2" {{ request('class') === 'Class 2' ? 'selected' : '' }}>Class 2</option>
            <option value="Class 3" {{ request('class') === 'Class 3' ? 'selected' : '' }}>Class 3</option>
        </select>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg">Search</button>
        @if(request()->hasAny(['search', 'class']))
            <a href="{{ route('admin.wards.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg">Clear</a>
        @endif
    </form>
</div>

<!-- Wards Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ward Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beds</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($wards as $ward)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-mono font-semibold rounded bg-gray-100 text-gray-800">
                        {{ $ward->code }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $ward->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        @if($ward->class === 'VIP') bg-purple-100 text-purple-800
                        @elseif($ward->class === 'Class 1') bg-blue-100 text-blue-800
                        @elseif($ward->class === 'Class 2') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $ward->class }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    @if($ward->building)
                        {{ $ward->building }}
                    @endif
                    @if($ward->floor)
                        - Floor {{ $ward->floor }}
                    @endif
                    @if(!$ward->building && !$ward->floor)
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        <span class="font-semibold">{{ $ward->available_beds_count ?? 0 }}</span> / {{ $ward->beds_count ?? 0 }} available
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.wards.edit', $ward) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                    <form action="{{ route('admin.wards.destroy', $ward) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this ward?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                    <p class="text-lg">No wards found</p>
                    <p class="text-sm mt-1">Get started by creating a new ward.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($wards->hasPages())
    <div class="mt-6">
        {{ $wards->links() }}
    </div>
@endif
@endsection
