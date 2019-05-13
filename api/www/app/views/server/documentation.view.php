<nav class="bg-primary fixed">
  <div class="container">

    <div class="row justify-content-center">
      <div class="col-md-6">

        <form class="form-api_doc_token py-2" action="/documentation/change-token" method="post">

          <div class="input-group">
            <input id="api_doc_token" placeholder="Access token" name="api_doc_token" class="form-control" value="<?= $_SESSION['api_doc_token']??"" ?>" type="text">
            <div class="input-group-append">
              <button class="btn btn-success bg-success text-white input-group-text"><i class="fa fa-arrow-up text-white"></i></button>
            </div>
          </div>

        </form>

      </div>
    </div>

  </div>
</nav>

<main>
  <nav class="transform navbar-side">
    <div class="title">Summary</div>
    <ul>
      <?php foreach ($documentation as $model => $routes): ?>
        <li><a href="#model-<?= strtolower($model) ?>" class="js-scroll-to"><i class="fa fa-hashtag"></i> <?= $model ?></a></li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <a  href="/documentation/create-schema" class="btn btn-block btn-success mt-3 transform">Create or Recreate schema</a>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-6">

        <?php foreach ($documentation as $model => $routes): ?>

          <div id="model-<?= strtolower($model) ?>" class="transform alert alert-primary mt-4 show-structure" data-toggle="modal" data-target="#modal-<?=$model?>">
            <div id="<?= lcfirst($model) ?>">
               <h2> <i class="fa fa-hashtag"></i> <?= $model ?></h2>
            </div>
          </div>

        <div class="accordion mb-3" id="<?= '_parent' ?>">
          <?php foreach ($routes as $route): ?>

            <?php
              $bg = '';
              switch ($route['method']) {
                case 'get':
                  $bg = 'primary';
                  break;
                case 'post':
                  $bg = 'success';
                  break;
                case 'put':
                  $bg = 'warning';
                  break;
                case 'delete':
                  $bg = 'danger';
                  break;
                default:
                  $bg = 'primary';
                  break;
              }
            ?>


              <div class="card transform <?= $route['method'] ?>">
                <div class="card-header header-doc-endpoint <?= $route['method'] ?> text-white" id="headingOne" data-toggle="collapse" data-target="#<?= $route["HTMLid"] ?>" aria-expanded="false" aria-controls="<?= $route["HTMLid"] ?>">
                  <div class="endpoint" >
                    <?= $route['endpoint'] ?>
                  </div>
                  <div class="method">
                    <?php if ($route['private']): ?>
                      <i class="fa fa-lock px-2"></i>
                    <?php endif; ?>
                    <?= strtoupper(' '.$route['method']) ?>
                  </div>
                </div>

                <div id="<?= $route["HTMLid"] ?>" class="collapse" aria-labelledby="headingOne" >
                  <div class="card-body">

                    <?php if ($route['private']): ?>
                      <div class="alert alert-danger">
                          <i class="fa fa-lock px-2"></i> A valid access token is required for this endpoint.
                      </div>
                    <?php endif; ?>

                    <?php if ($route['method'] === 'put'): ?>
                      <div class="alert alert-warning">
                        For PUT request, please set the Content-Type to 'application/x-www-form-urlencoded'.
                      </div>
                    <?php endif; ?>

                    <div class="title">Description</div>
                    <div class="description">
                      <?= $route['description']??"<em>Not set.</em>" ?>
                    </div>

                    <!-- URLParameters -->
                    <?php if (isset($route['URLParameters'])): ?>
                      <div class="title">Parameters in slug</div>
                      <div class="url-parameters mb-2">
                        <?php foreach ($route['URLParameters'] as $param ): ?>
                          <a class="badge text-white px-1 py-1 <?= $route['method'] ?>"><?= $param ?></a>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($route['requiredFields'])): ?>
                      <div class="title">Required field(s)</div>
                      <div class="required">
                        <?php
                            $json = APIDocumentation::toJSON($route['requiredFields']);
                            echo '<pre>';
                            echo $json;
                            echo '</pre>';
                        ?>
                      </div>
                    <?php endif; ?>

                    <?php if ($route['returns']??false): ?>
                      <div class="title">Reponse</div>
                      <div class="returns">
                        <?php
                          $json = "{\n";
                          foreach ($route['returns'] as $key => $type) {
                            $value = "";
                            if ($type == 'object') { $value = "{}"; }
                            if ($type == 'array') { $value = "[]"; }
                            if ($type == 'string') { $value = "\"\""; }
                            $json .= "\t\"$key\": $value,\n";
                          }
                          $json .= "}";

                          echo '<pre>';
                          echo $json;
                          echo '</pre>';
                        ?>
                      </div>
                    <?php endif; ?>

                    <!-- FORMS -->
                    <button class="btn btn-block btn-dark text-white" type="button" data-toggle="collapse" data-target="#<?= $route['HTMLid'].'-tester' ?>" aria-expanded="false" aria-controls="<?= $route['HTMLid'].'-tester' ?>">
                      Click here to test it!
                    </button>
                    <div class="collapse mt-3" id="<?= $route['HTMLid'].'-tester' ?>">
                      <div class="card card-body">
                        <div class="title mb-3">Why don't you try this endpoint?</div>
                        <form class="form-tester" action="<?= $route['endpoint'] ?>" method="<?= $route['method'] ?>">
                          <div class="input-container">
                            <?php if (isset($route['requiredFields']) && ($route['method'] !== 'get' && $route['method'] !== 'delete')): ?>
                              <?php foreach ($route['requiredFields'] as $key): ?>


                                <div class="input-group mb-3 default-input input <?= strtolower($key) ?>">
                                  <input type="text" class="form-control mt-2" name="<?= strtolower($key) ?>" type="text" placeholder="<?= ucfirst($key) ?>">
                                  <div class="input-group-append mt-2">
                                    <button class="btn-danger bg-danger text-white  btn input-group-text default-remove-input <?= strtolower($key) ?>">
                                      <small>Supprimer</small>
                                    </button>
                                  </div>
                                </div>

                              <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (isset($route['schema']) && $route['method'] !== 'get' && $route['method'] !== 'delete'): ?>
                              <div class="title mb-3">Add field</div>
                              <div class="input-group mb-3">
                                <select class="form-control select-input-tester" name="">
                                  <?php foreach ($route['schema'] as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= ucfirst($key) ?></option>
                                  <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                  <button class="input-group-text btn add-input">Ajouter</span>
                                </div>
                                <!-- add here -->
                              </div>
                            <?php endif; ?>
                            <?php if (isset($route['URLParameters']) && ($route['method'] === 'get' || $route['method'] === 'delete')): ?>
                              <?php foreach ($route['URLParameters'] as $key): ?>
                                <div class="input-group mb-3 mt-2">
                                  <input type="text" class="form-control" name="<?= $key ?>" placeholder="<?= ucfirst(str_replace(':', '', $key)) ?>" value="">
                                </div>
                              <?php endforeach; ?>
                            <?php endif; ?>
                            <button class="btn btn-success input-tester-submit try-it-out">Try it out !</button>
                          </div>
                        </form>

                      </div>
                    </div>

                  </div>
                </div>
              </div>
          <?php endforeach; ?>

        </div>

          <!-- Modal -->
          <div class="modal fade" id="modal-<?= $model?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">
                      Model information <br>
                      <span class="modal-model-name"><?= $model ?></span>
                      (key column: <?= $route["keyColumn"]??"<em>Not set.<em>" ?>)
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body text-left">
                  <div class="py-2 title">Structure</div>
                  <?php
                    // Show structure
                    $json = APIDocumentation::toJSON($route["schema"], true);
                    echo '<pre>';
                    echo $json;
                    echo '</pre>';
                  ?>
                  <div class="py-2 title">Unique columns</div>
                  <?php
                    $json = APIDocumentation::toJSON($route['uniqueColumns']);
                    echo '<pre>';
                    echo $json;
                    echo '</pre>';
                  ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </div>
</main>
