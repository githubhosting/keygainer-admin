<h2>Premium Quotes</h2>
<hr />

<div class="row">
    <div class="col-lg-12">

        <h4>Add a Quotes</h4>
        
        <form id="image-form">

            <div class="form-group">
                <label for="category">Select Category</label>
                <select id="category" class="form-control">

                </select>
            </div>

            <div class="form-group">
                <label for="title">Quote Text</label>
                <textarea type="text" class="form-control" id="title" />
                <div class="invalid-feedback"></textarea> 
                    Please enter title 
                </div>
            </div>

 

            <div class="form-group">
                <label for="desc">Description</label>
                
                <input name="desc" id="desc" value="YMG-Developers" class="form-control" disabled/>
                                                         
                                                    
                <div class="invalid-feedback">
                    Please enter description 
                </div>                    
            </div>

            <div class="form-group">
                <div class="progress">
                    <div class="progress-bar" id="progress" style="width:0%">0%</div>
                </div>
            </div>

            <div class="form-group">
                <button type="button" id="btn-save" class="btn btn-primary">Save Quotes</button>
            </div>

        </form>

    </div>
</div>

<script>
    function previewWallpaper(thumbnail){
        if(thumbnail.files && thumbnail.files[0]){
            var reader = new FileReader(); 

            reader.onload = function(e){
                $("#img-wallpaper").attr('src', e.target.result);
            }
            reader.readAsDataURL(thumbnail.files[0]);
        }
    }

    $("#wallpaper").change(function(){
        previewWallpaper(this);
    });

    var dbCategories = firebase.database().ref("categories");

    dbCategories.once("value").then(function(categories){

        categories.forEach(function(category){
            $("#category").append("<option value='"+category.key+"'>"+category.key+"</option>");     
             
        });
    });

    var dbCategories1 = firebase.database().ref("authors");

    dbCategories1.once("value").then(function(categories){

        categories.forEach(function(category){
            $("#author").append("<option value='"+category.key+"'>"+category.key+"</option>");     
             
        });
    });

    var validImageTypes = ["text", "image/jpeg", "image/png"];

    $("#btn-save").click(function(){
        $("#title").removeClass("is-invalid");
        $("#desc").removeClass("is-invalid");    
        

        var title = $("#title").val();
        var desc = $("#desc").val(); 
        

        if(!title){
            $("#title").addClass("is-invalid");
            return; 
        }

        if(!desc){
            $("#desc").addClass("is-invalid");
            return; 
        }


        

        var category = $("#category").val();
        var author = $("#author").val();
    
        
                    var database = firebase.database().ref("ymg");
                    
                    var imageid = database.push().key;

                    var image = {
                       
                    
                        "category": category,
                        "title": title,
                        "id": <?php echo(rand(10,1000));
?>
                    };

                    database.child(imageid).set(image, function(err){
                        alert("Image saved..");
                        resetForm();
                    });

        

    });
    
    function resetForm(){
       $("#image-form")[0].reset(); 
       $("#img-wallpaper").attr("src", "");;
       $("#progress").html("Completed");
location.reload();
    }
</script>