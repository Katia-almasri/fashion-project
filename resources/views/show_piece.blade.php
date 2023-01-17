<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<table class="table">
  <thead>
    <tr>
      <th scope="col">id</th>
      <th scope="col">Name</th>
      <th scope="col">usage name</th>
      <th scope="col">usage id</th>
      
    </tr>
  </thead>
  <tbody>
      @foreach($pieces as $piece)
    <tr>
      <td>{{$piece->id}}</td>
      <td>{{$piece->name}}</td>
      <td>{{$piece->usage->name}}</td>
      <td>{{$piece->usage->id}}</td>
      <td>
      {{-- <a href="{{route('edit.doctor' , $doc->id)}}" type="button" class="btn btn-primary">Edit</button>
        <a href="{{route('delete.doctor' , $doc->id)}}" type="button" class="btn btn-danger">Delete</button></td> --}}
    </tr>
    @endforeach
  </tbody>
</table>