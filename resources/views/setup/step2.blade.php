@extends('setup.main')
@section('content')
<div class="row">
   <div class="col-12 text-center mt-3">
      <ul class="progressbar">
    ,    <li class="active"><a href="/setup">Server Requirements</a></li>
         <li class="active"><a href="/setup/step-1">Settings</a></li>
         <li class="active"> <a href="/setup/step-2">Database</a></li>
         <li>Summary</li>
      </ul>
   </div>
</div>
<div class="row mt-3 p-5">
   <div class="col-12">
      <form id="dbform" action="{{route('setupStep2')}}" method="post">
         @csrf
         <div id="errormsg"></div>
            <div id="db_settings" class="form-group"></div>
                <label for="app_env">Select Database Type</label>
                <span class="tip" title="The type of your database">
                    <i class="fa fa-question-circle" aria-hidden="true"></i></span>
                <select class="form-control" id="db_connection" name="db_connection">
                    <option value="mysql" {{ (isset($data['DB_CONNECTION']) && $data['DB_CONNECTION'] == 'mysql') ? 'selected' : '' }}>MySQL</option>
                    <option value="sqlite" {{ (isset($data['DB_CONNECTION']) && $data['DB_CONNECTION'] == 'sqlite') ? 'selected' : '' }}>SQLite</option>
                </select>
                <div id="mysql_fields" style="display: {{ (isset($data['DB_CONNECTION']) && $data['DB_CONNECTION'] == 'mysql') ? 'block' : 'none' }};">
                    <label for="app_name" class="mt-1" id="db_host_label">DB Host</label>
                        <span class="tip" id="db1tooltip" title="The ip or domain your database server is hosted. For local development this usually is 127.0.0.1">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </span>
                    <input type="text" class="form-control" id="db_host" name="db_host" placeholder="127.0.0.1" value="{{$data["DB_HOST"]}}">
                    <label for="app_name" class="mt-1" id="db_port_label">DB Port</label>
                         <span class="tip" id="db2tooltip" title="The port on which your database is running">
                             <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </span>
                    <input type="text" class="form-control" id="db_port" name="db_port" placeholder="3306" value="{{$data["DB_PORT"]}}">
                    <label for="app_name" class="mt-1" id="db_database_label">DB Database</label> 
                        <span class="tip" title="The name of your database">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </span>
                    <input type="text" class="form-control" id="db_database" name="db_database" placeholder="Database Name" value="{{$data["DB_DATABASE"]}}">
                    <label for="app_name" class="mt-1" id="db_username_label">DB Username</label> 
                        <span class="tip" id="db3tooltip" title="The username for your database connection">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                         </span>
                    <input type="text" class="form-control" id="db_username" name="db_username" placeholder="Username" value="{{$data["DB_USERNAME"]}}">
                    <label for="app_name" class="mt-1" id="db_password_label">DB Password</label> 
                        <span class="tip"  id="db4tooltip"title="The password for your database connection">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </span>
                    <input type="text" class="form-control" id="db_password" name="db_password" placeholder="Password" value="{{$data["DB_PASSWORD"]}}">
                </div>
                <div id="sqlite_fields" style="display: {{ (isset($data['DB_CONNECTION']) && $data['DB_CONNECTION'] == 'sqlite') ? 'block' : 'none' }};">
                    <label for="app_name" class="mt-1" id="db_database_label">SQLite Database File</label>
                        <span class="tip" title="The path to your SQLite database file (e.g. database/database.sqlite)">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </span>
                    <input type="text" class="form-control" id="db_database" name="db_database" placeholder="database/database.sqlite" value="{{$data["DB_DATABASE"]}}">
                </div>
            <a id="testdb" class="btn btn-dark mb-2 form-control mt-2 text-white"> Test Connection
                <i class="fa fa-question-circle-o "></i>
                <span id="testdb_tip_mysql" class="tip" style="display: {{ (isset($data['DB_CONNECTION']) && $data['DB_CONNECTION'] == 'mysql') ? 'inline' : 'none' }};" title="Test connection to MySQL database."></span>
                <span id="testdb_tip_sqlite" class="tip" style="display: {{ (isset($data['DB_CONNECTION']) && $data['DB_CONNECTION'] == 'sqlite') ? 'inline' : 'none' }};" title="Test if the SQLite database file exists and is accessible."></span>
            </a>
            <script>
                document.getElementById('db_connection').addEventListener('change', function() {
                    var type = this.value;
                    document.getElementById('mysql_fields').style.display = type === 'mysql' ? 'block' : 'none';
                    document.getElementById('sqlite_fields').style.display = type === 'sqlite' ? 'block' : 'none';
                    document.getElementById('testdb_tip_mysql').style.display = type === 'mysql' ? 'inline' : 'none';
                    document.getElementById('testdb_tip_sqlite').style.display = type === 'sqlite' ? 'inline' : 'none';
                });
            </script>
            <div class="row">
                <div class="col-12 col-md-6">
                 <a href="/setup/step-1" class="btn btn-outline-danger mb-2"  ><i class="fa fa-angle-left"></i> Previous Step </a>
                </div>
                <div class="col-12 col-md-6">
                <button type="submit" class="btn btn-outline-danger mb-2  float-md-right next_step d-none"> Next Step <i class="fa fa-angle-right"></i></button>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>
@endsection
