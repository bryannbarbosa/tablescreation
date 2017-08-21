<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/api/auth', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    if (array_key_exists('email', $data) && array_key_exists('password', $data)) {
        $account = $db->select("users", "*", [
        "AND" => [
        "email" => $data['email'],
        "password" => $data['password']
        ]
    ]);

        $id = $db->id();

        if (count($account) > 0) {
            $key = 'bobsponja56';
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $header = json_encode($header);
            $header = base64_encode($header);

            $payload = [
                'iss' => 'localhost:8000',
                'id_user' => $account[0]['id'],
                'email' => $account[0]['email']
            ];

            $payload = json_encode($payload);
            $payload = base64_encode($payload);

            $signature = hash_hmac('sha256', "$header.$payload", $key, true);

            $signature = base64_encode($signature);

            $token = "$header.$payload.$signature";

            return $response->withJson(array(
            'response' => $token
        ));
        } else {
            return $response->withJson(array(
            'response' => 'Not Authenticated!'
        ));
        }
    } else {
        return $response->withJson(array(
            'response' => 'E-mail and password are required.'
        ));
    }
});

$app->get('/api/tables', function (Request $request, Response $response) {
    global $db;
    $data = $db->select('agreements', '*');

    for ($i = 0; $i < count($data); $i++) {
        $consult_tbody = $db->select("trs_tbody", "*", [
        "id_agreement" => $data[$i]['id']
        ]);
        $consult_thead = $db->select("trs_thead", "*", [
        "id_agreement" => $data[$i]['id']
        ]);
        $data[$i]['thead_tr'] = $consult_thead;
        $data[$i]['tbody_tr'] = $consult_tbody;
    }

    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[$i]['thead_tr']); $j++) {
            $consult_th = $db->select("ths_thead", "*", [
            "id_tr" => $data[$i]['thead_tr'][$j]['id']
            ]);
            $data[$i]['thead_tr'][$j]['ths'] = $consult_th;
        }
    }

    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[$i]['tbody_tr']); $j++) {
            $consult_td = $db->select("tds_tbody", "*", [
            "id_tr" => $data[$i]['tbody_tr'][$j]['id']
            ]);
            $data[$i]['tbody_tr'][$j]['tds'] = $consult_td;
        }
    }

    return $response->withJson($data);
});

$app->post('/api/tables', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['id'] == null) {
            $db->insert('agreements', [
            'agreement_name' => $data[$i]['agreement_name'],
            'agreement_image_url' => $data[$i]['agreement_image_url'],
            'open_agreement' => $data[$i]['open_agreement']
            ]);

            $id = $db->id();

            if ($id == 0) {
                return $response->withJson(array(
                    'response' => 'Error in creating tables'
                ));
            } else {
                for ($j = 0; $j < count($data[$i]['thead_tr']); $j++) {
                    $db->insert('trs_thead', [
                    'id_agreement' => $id
                    ]);

                    $id_thead_tr = $db->id();
                    if ($id_thead_tr == 0) {
                        return $response->withJson(array(
                        'response' => 'Error in creating thead_tr'
                        ));
                    }

                    for ($k = 0; $k < count($data[$i]['thead_tr'][$j]['ths']); $k++) {
                        $db->insert('ths_thead', [
                        'id_tr' => $id_thead_tr,
                        'th_value' => $data[$i]['thead_tr'][$j]['ths'][$k]['th_value']
                        ]);

                        $id_thead_th = $db->id();
                        if ($id_thead_th == 0) {
                            return $response->withJson(array(
                          'response' => 'Error in creating thead_th'
                          ));
                        }
                    }
                }

                for ($j = 0; $j < count($data[$i]['tbody_tr']); $j++) {
                    $db->insert('trs_tbody', [
                    'id_agreement' => $id
                    ]);

                    $id_tbody_tr = $db->id();
                    if ($id_tbody_tr == 0) {
                        return $response->withJson(array(
                        'response' => 'Error in creating tbody_tr'
                    ));
                    }

                    for ($k = 0; $k < count($data[$i]['tbody_tr'][$j]['tds']); $k++) {
                        $db->insert('tds_tbody', [
                        'id_tr' => $id_tbody_tr,
                        'td_value' => $data[$i]['tbody_tr'][$j]['tds'][$k]['td_value']
                        ]);

                        $id_tbody_td = $db->id();
                        if ($id_tbody_td == 0) {
                            return $response->withJson(array(
                          'response' => 'Error in creating tbody_td'
                          ));
                        }
                    }
                }
            }
        } else {
            $db->update('agreements', [
            'agreement_name' => $data[$i]['agreement_name']
            ], [
            'AND' => [
            'id' => $data[$i]['id'],
            'agreement_name[!]' => $data[$i]['agreement_name']
             ]
            ]);

            $db->update('agreements', [
            'agreement_image_url' => $data[$i]['agreement_image_url']
            ], [
            'AND' => [
            'id' => $data[$i]['id'],
            'agreement_image_url[!]' => $data[$i]['agreement_image_url']
             ]
            ]);

            $db->update('agreements', [
            'open_agreement' => $data[$i]['open_agreement']
            ], [
            'AND' => [
            'id' => $data[$i]['id'],
            'open_agreement[!]' => $data[$i]['open_agreement']
             ]
            ]);

            for ($m = 0; $m < count($data[$i]['thead_tr']); $m++) {
                for ($n = 0; $n < count($data[$i]['thead_tr'][$m]['ths']); $n++) {
                    $db->update('ths_thead', [
                    'th_value' => $data[$i]['thead_tr'][$m]['ths'][$n]['th_value']
                    ], [
                    'AND' => [
                    'id' => $data[$i]['thead_tr'][$m]['ths'][$n]['id'],
                    'th_value[!]' => $data[$i]['thead_tr'][$m]['ths'][$n]['th_value']
                    ]
                    ]);
                }
            }
        }
    }
});

// Delete Table

$app->post('/api/tables/delete', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $professions = $db->select("professions", "*", [
      "id_agreement" => $data['id']
    ]);

    $trs_thead = $db->select("trs_thead", "*", [
        "id_agreement" => $data['id']
        ]);

    $trs_tbody = $db->select("trs_tbody", "*", [
        "id_agreement" => $data['id']
        ]);


    for ($i = 0; $i < count($trs_thead); $i++) {
        $db->delete("ths_thead", [
            "id_tr" => $trs_thead[$i]['id']
            ]);
    }

    for ($i = 0; $i < count($trs_tbody); $i++) {
        $db->delete("tds_tbody", [
            "id_tr" => $trs_tbody[$i]['id']
            ]);
    }

    for ($i = 0; $i < count($trs_tbody); $i++) {
        $db->delete("ages_tbody_tr", [
            "id_tbody_tr" => $trs_tbody[$i]['id']
            ]);
    }

    $db->delete("trs_thead", [
            "id_agreement" => $data['id']
            ]);

    $db->delete("trs_tbody", [
            "id_agreement" => $data['id']
            ]);

    $db->delete("professions", [
                "id_agreement" => $data['id']
    ]);

    $table = $db->delete("agreements", [
            "id" => $data['id']
            ]);


    return $response->withJson(array(
            'response' => $table->rowCount()
        ));
});

// Update Th in table

$app->post('/api/tables/th/update/{id}', function (Request $request, Response $response, $args) {
    global $db;
    $data = $request->getParams();

    $db->update("ths_thead", [
    "th_value" => $data['value'],
  ], [
      "id" => $args['id']
  ]);
    $count = $db->rowCount();
    if ($count > 0) {
        return $response->withJson(array(
      'response' => 'Th is updated!'
    ));
    } else {
        return $response->withJson(array(
      'response' => 'Error in updating th'
    ));
    }
});

$app->post('/api/tables/td/update/{id}', function (Request $request, Response $response, $args) {
    global $db;
    $data = $request->getParams();

    $result = $db->update("tds_tbody", [
    "td_value" => $data['value'],
  ], [
      "id" => $args['id']
  ]);

    $count = $result->rowCount();
    if ($count > 0) {
        return $response->withJson(array(
      'response' => 'Th is updated!'
    ));
    } else {
        return $response->withJson(array(
      'response' => 'Error in updating th'
    ));
    }
});

// Delete Th from tables

$app->delete('/api/tables/th/delete/{id}', function (Request $request, Response $response, $args) {
    global $db;
    $data = $request->getParams();

    $result = $db->delete("ths_thead", [
      "AND" => [
          "id" => $args['id']
      ]
    ]);

    $count = $result->rowCount();

    if ($count > 0) {
        return $response->withJson(array(
      'response' => 'Th is deleted!'
    ));
    } else {
        return $response->withJson(array(
      'response' => 'Error in deleting th'
    ));
    }
});

$app->delete('/api/tables/td/delete/{id}', function (Request $request, Response $response, $args) {
    global $db;
    $data = $request->getParams();

    $result = $db->delete("tds_tbody", [
      "AND" => [
          "id" => $args['id']
      ]
    ]);

    $count = $result->rowCount();

    if ($count > 0) {
        return $response->withJson(array(
      'response' => 'Td is deleted!'
    ));
    } else {
        return $response->withJson(array(
      'response' => 'Error in deleting td'
    ));
    }
});

$app->post('/api/tables/th/create', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $db->insert('ths_thead', [
    'id_tr' => $data['id_tr'],
    'th_value' => $data['value']
    ]);

    $count = $db->id();

    if ($count > 0) {
        return $response->withJson(array(
        'response' => 'th created with success'
      ));
    } else {
        return $response->withJson(array(
        'response' => 'error in creating th'
      ));
    }
});

$app->post('/api/tables/td/create', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $db->insert('tds_tbody', [
    'id_tr' => $data['id_tr'],
    'td_value' => $data['value']
    ]);

    $count = $db->id();

    if ($count > 0) {
        return $response->withJson(array(
        'response' => 'td created with success'
      ));
    } else {
        return $response->withJson(array(
        'response' => 'error in creating td'
      ));
    }
});

$app->get('/api/profession', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $profession = $db->query("SELECT * FROM professions")->fetchAll();

    return $response->withJson($profession);
});

$app->get('/api/profession/distinct', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $profession = $db->query("select distinct profession_name from professions")->fetchAll();

    return $response->withJson($profession);
});

$app->post('/api/profession', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();
    $db->insert("professions", [
    "id_agreement" => $data['id_table'],
    "profession_name" => $data['profession'],
    ]);
    $id = $db->id();
    if ($id > 0) {
        return $response->withJson(array(
            'response' => 'Profession inserted successfully!'
        ));
    } else {
        return $response->withJson(array(
            'response' => 'Error in inserting profession'
        ));
    }
});

$app->post('/api/profession/remove', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $db->delete("professions", [
    "AND" => [
        "id_agreement" => $data['id_table'],
        "id" => $data['id_profession']
    ]
    ]);

    return $response->withJson(array(
        'response' => 'error!'
    ));
});

$app->post('/api/ages/insert', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $db->insert("ages", [
    "age_name" => $data['age_name'],
    "age_initial" => $data['age_initial'],
    "age_final" => $data['age_final']
    ]);
    $id = $db->id();
    if ($id > 0) {
        return $response->withJson(array(
            'response' => 'Age inserted successfully!'
        ));
    } else {
        return $response->withJson(array(
            'response' => 'Error in inserting age'
        ));
    }
});

$app->delete('/api/ages/delete/{id}', function (Request $request, Response $response, $args) {
    global $db;

    $delete_relation = $result = $db->delete("ages_tbody_tr", [
    "AND" => [
        "id_age" => $args['id']
      ]
    ]);

    $result = $db->delete("ages", [
    "AND" => [
        "id" => $args['id']
      ]
    ]);

    $id_age = $result->rowCount();
    $id_tr = $delete_relation->rowCount();
    if ($id_tr > 0 && $id_age > 0) {
        return $response->withJson(array(
            'response' => 'Age deleted with successfully!'
        ));
    } else {
        return $response->withJson(array(
            'response' => 'Error in deleting age'
        ));
    }
});

$app->post('/api/ages/tbody/tr/insert', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $db->insert("ages_tbody_tr", [
    "id_age" => $data['id_age'],
    "id_tbody_tr" => $data['id_tbody_tr'],
    ]);
    $id = $db->id();
    if ($id > 0) {
        return $response->withJson(array(
            'response' => 'Relation inserted successfully!'
        ));
    } else {
        return $response->withJson(array(
            'response' => 'Error in inserting relation'
        ));
    }
});

$app->post('/api/ages/tbody/tr/delete', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $result = $db->delete("ages_tbody_tr", [
    "AND" => [
        "id_age" => $data['id_age'],
        "id_tbody_tr" => $data['id_tr']
      ]
    ]);

    $id = $result->rowCount();

    if($id > 0) {
      return $response->withJson(array(
        'response' => 'Relation deleted successfully'
      ));
    } else {
      return $response->withJson(array(
        'response' => 'Error in deleting relation'
      ));
    }

});

$app->get('/api/ages', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $data = $db->select('ages', '*');
    return $response->withJson($data);
});

$app->get('/api/ages/tbody/tr', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $data = $db->select('ages_tbody_tr', '*');
    return $response->withJson($data);
});

$app->post('/api/tables/thead/tr/create', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $db->insert('trs_thead', [
    'id_agreement' => $data['id_table']
    ]);

    $count_tr = $db->id();

    $db->insert('ths_thead', [
    'id_tr' => $count_tr,
    'th_value' => 'Nova Coluna'
    ]);

    $count_th = $db->id();

    if ($count_tr > 0 && $count_th > 0) {
        return $response->withJson(array(
        'response' => 'tr and th created with success'
      ));
    } else {
        return $response->withJson(array(
        'response' => 'error in creating tr and th'
      ));
    }
});

$app->post('/api/tables/tbody/tr/create', function (Request $request, Response $response) {
    global $db;
    $data = $request->getParams();

    $db->insert('trs_tbody', [
    'id_agreement' => $data['id_table']
    ]);

    $count_tr = $db->id();

    $db->insert('tds_tbody', [
    'id_tr' => $count_tr,
    'td_value' => 'Novo Valor'
    ]);

    $count_th = $db->id();

    if ($count_tr > 0 && $count_th > 0) {
        return $response->withJson(array(
        'response' => 'tr and td created with success'
      ));
    } else {
        return $response->withJson(array(
        'response' => 'error in creating tr and td'
      ));
    }
});
