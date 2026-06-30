@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Search Freesound</h1>

        <form action="{{ route('freesound.search') }}" method="GET">
            <div class="mb-3">
                <label for="query" class="form-label">Query</label>
                <input type="text" name="query" id="query" class="form-control"
                       value="{{ request('query') }}" placeholder="e.g. piano, rain, footsteps...">
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="sort" class="form-label">Sort by</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="score" @selected(request('sort') === 'score')>Relevance</option>
                        <option value="duration_desc" @selected(request('sort') === 'duration_desc')>Duration (longest)</option>
                        <option value="duration_asc" @selected(request('sort') === 'duration_asc')>Duration (shortest)</option>
                        <option value="created_desc" @selected(request('sort') === 'created_desc')>Newest</option>
                        <option value="created_asc" @selected(request('sort') === 'created_asc')>Oldest</option>
                        <option value="downloads_desc" @selected(request('sort') === 'downloads_desc')>Most downloaded</option>
                        <option value="rating_desc" @selected(request('sort') === 'rating_desc')>Highest rated</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="page_size" class="form-label">Results per page</label>
                    <select name="page_size" id="page_size" class="form-select">
                        <option value="15" @selected(request('page_size', 15) == 15)>15</option>
                        <option value="30" @selected(request('page_size') == 30)>30</option>
                        <option value="50" @selected(request('page_size') == 50)>50</option>
                        <option value="100" @selected(request('page_size') == 100)>100</option>
                        <option value="150" @selected(request('page_size') == 150)>150</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="filter" class="form-label">Filter (optional)</label>
                    <input type="text" name="filter" id="filter" class="form-control"
                           value="{{ request('filter') }}"
                           placeholder="e.g. tag:piano type:wav duration:[5 TO *]">
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="group_by_pack" id="group_by_pack" value="1"
                       class="form-check-input" @checked(request()->boolean('group_by_pack'))>
                <label for="group_by_pack" class="form-check-label">Group results by pack</label>
            </div>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
@endsection
