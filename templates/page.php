<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вызов врача на дом</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="doctor.php">Вызов врача</a>
        <div class="navbar-nav ms-auto">
            <?php if (user()): ?>
                <a class="nav-link" href="doctor.php?action=cabinet">Кабинет</a>
                <form action="doctor.php?action=logout" method="post" style="display:inline"><?= Security::field() ?><button type="submit" class="nav-link" >Выход</button></form>
            <?php else: ?>
                <a class="nav-link" href="doctor.php?action=register">Регистрация</a>
                <a class="nav-link" href="doctor.php?action=login">Вход</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($action === 'home'): ?>
        <div class="bg-white border rounded p-4">
            <h1 class="h3 mb-3">Вызов врача на дом</h1>
            <p>Оставьте заявку на вызов врача и отслеживайте её статус в личном кабинете.</p>

            <div class="d-flex gap-2">
                <?php if (user()): ?>
                    <a href="doctor.php?action=new_request" class="btn btn-primary">Новая заявка</a>
                    <a href="doctor.php?action=cabinet" class="btn btn-outline-primary">Кабинет</a>
                <?php else: ?>
                    <a href="doctor.php?action=register" class="btn btn-primary">Регистрация</a>
                    <a href="doctor.php?action=login" class="btn btn-outline-primary">Вход</a>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($action === 'register'): ?>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h3 mb-3">Регистрация</h1>
                        <form method="post" action="doctor.php?action=register"><?= Security::field() ?>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Имя</label>
                                <input type="text" name="name" class="form-control" required minlength="5" value="<?= e($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Телефон</label>
                                <input type="tel" name="phone" class="form-control" required pattern="^\+?[0-9\-\(\)\s]{10,20}$" oninput="this.value=this.value.replace(/[^0-9+() -]/g,'')" value="<?= e($_POST['phone'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Пароль</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <button class="btn btn-primary" type="submit">Зарегистрироваться</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'login'): ?>
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h3 mb-3">Вход</h1>
                        <form method="post" action="doctor.php?action=login"><?= Security::field() ?>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Пароль</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary" type="submit">Войти</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'new_request'): ?>
        <?php if (!user()):
            go('doctor.php?action=login'); endif; ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h3 mb-3">Новая заявка</h1>
                        <form method="post" action="doctor.php?action=save_request"><?= Security::field() ?>
                            <div class="mb-3">
                                <label class="form-label">ФИО пациента</label>
                                <input type="text" name="patient_name" class="form-control" required minlength="5" value="<?= e($_POST['patient_name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Адрес проживания</label>
                                <input type="text" name="address" class="form-control" required minlength="10" value="<?= e($_POST['address'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Симптомы</label>
                                <textarea name="symptoms" class="form-control" rows="4" required minlength="5"><?= e($_POST['symptoms'] ?? '') ?></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">Сохранить заявку</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'cabinet'): ?>
        <?php if (!user()):
            go('doctor.php?action=login'); endif; ?>

        <?php if (isAdmin()): ?>
            <h1 class="h3 mb-3">Панель администратора</h1>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <th>Пользователь</th>
                                <th>Телефон</th>
                                <th>Пациент</th>
                                <th>Адрес</th>
                                <th>Симптомы</th>
                                <th>Статус</th>
                                <th>Изменить</th>
                            </tr>
                            <?php foreach ($allRequests as $request): ?>
                                <tr>
                                    <td><?= (int) $request['id'] ?></td>
                                    <td><?= e($request['user_name']) ?><br><small><?= e($request['email']) ?></small></td>
                                    <td><?= e($request['phone']) ?></td>
                                    <td><?= e($request['patient_name']) ?></td>
                                    <td><?= e($request['address']) ?></td>
                                    <td><?= e($request['symptoms']) ?></td>
                                    <td><?= e($statusLabels[$request['status']]) ?></td>
                                    <td>
                                        <form method="post" action="doctor.php?action=set_status"><?= Security::field() ?>
                                            <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                            <select name="status" class="form-select form-select-sm mb-2">
                                                <?php foreach ($statusLabels as $value => $label): ?>
                                                    <option value="<?= e($value) ?>" <?= $request['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-sm btn-primary" type="submit">OK</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0">Личный кабинет</h1>
                <a class="btn btn-primary" href="doctor.php?action=new_request">Подать заявку</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <th>ФИО пациента</th>
                                <th>Адрес</th>
                                <th>Симптомы</th>
                                <th>Статус</th>
                                <th>Дата</th>
                            </tr>
                            <?php foreach ($myRequests as $request): ?>
                                <tr>
                                    <td><?= (int) $request['id'] ?></td>
                                    <td><?= e($request['patient_name']) ?></td>
                                    <td><?= e($request['address']) ?></td>
                                    <td><?= e($request['symptoms']) ?></td>
                                    <td><?= e($statusLabels[$request['status']]) ?></td>
                                    <td><?= e($request['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
</body>
</html>
