<div>
            <form class="row col mt-5 text-center"> 
                <div class="form-group mb-2">
                    <label class="sr-only">Title</label>
                    <input type="text"  class="form-control-plaintext" value="Assign a User to Task: " readonly>
                    <input type="text"  class="sr-only" value="<?php echo $id; ?>" hidden>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label class="sr-only">User</label>
                    <select class="form-control">
                        <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_array($result)) {
                                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Assign</button>
            </form>
        </div>