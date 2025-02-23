<form method="POST" action="{{ route('healthdata.store') }}">
    @csrf
    <div>
        <label for="UserKey">User Key:</label>
        <input type="text" id="UserKey" name="UserKey" value="{{ old('UserKey') }}">
    </div>
    <div>
        <label for="Data">Comment about patient:</label>
        <input type="text" id="data" name="data" value="{{ old('data') }}">
    </div>
    <button type="submit">Submit</button>
</form>
