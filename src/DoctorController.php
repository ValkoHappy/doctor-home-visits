<?php
final class DoctorController
{
    public const STATUSES = ['new' => 'Новое', 'confirmed' => 'Подтверждено', 'rejected' => 'Отказано'];

    public function __construct(private AuthService $auth, private RequestRepository $requests) {}

    public function handle(): array
    {
        $action = $_GET['action'] ?? 'home';
        $errors = [];
        $success = '';
        $post = $_SERVER['REQUEST_METHOD'] === 'POST';
        if ($action === 'logout') {
            if (!$post) { http_response_code(405); exit; }
            session_destroy();
            go('doctor.php');
        }
        if ($action === 'register' && $post) {
            $email = trim((string) ($_POST['email'] ?? ''));
            $name = trim((string) ($_POST['name'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите корректный email.';
            if (mb_strlen($name) < 5 || mb_strlen($name) > 120) $errors[] = 'Введите имя от 5 до 120 символов.';
            if (!preg_match('/^\+?[0-9\-\(\)\s]{10,20}$/', $phone)) $errors[] = 'Введите корректный номер телефона.';
            if (strlen($password) < 8 || strlen($password) > 72) $errors[] = 'Пароль должен содержать от 8 до 72 символов.';
            if (!$errors) {
                if ($this->auth->register($email, $name, $phone, $password)) {
                    $success = 'Регистрация завершена. Теперь войдите.';
                    $action = 'login';
                } else $errors[] = 'Пользователь с таким email уже существует.';
            }
        }
        if ($action === 'login' && $post) {
            if ($this->auth->login(trim((string) ($_POST['email'] ?? '')), (string) ($_POST['password'] ?? ''))) go('doctor.php?action=cabinet');
            $errors[] = 'Неверный email или пароль.';
        }
        if ($action === 'save_request' && $post) {
            if (!user()) go('doctor.php?action=login');
            $name = trim((string) ($_POST['patient_name'] ?? ''));
            $address = trim((string) ($_POST['address'] ?? ''));
            $symptoms = trim((string) ($_POST['symptoms'] ?? ''));
            if (mb_strlen($name) < 5 || mb_strlen($name) > 150) $errors[] = 'Введите ФИО пациента.';
            if (mb_strlen($address) < 10 || mb_strlen($address) > 255) $errors[] = 'Введите полный адрес.';
            if (mb_strlen($symptoms) < 5 || mb_strlen($symptoms) > 5000) $errors[] = 'Опишите симптомы.';
            $action = 'new_request';
            if (!$errors) {
                $this->requests->create((int) user()['id'], $name, $address, $symptoms);
                $success = 'Заявка добавлена.';
                $action = 'cabinet';
            }
        }
        if ($action === 'set_status' && $post) {
            if (!isAdmin()) { http_response_code(403); exit; }
            $id = (int) ($_POST['request_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            if ($id > 0 && isset(self::STATUSES[$status])) {
                $this->requests->changeStatus($id, $status);
                $success = 'Статус обновлён.';
            } else $errors[] = 'Проверьте номер заявки и статус.';
            $action = 'cabinet';
        }
        return [
            'action' => $action, 'errors' => $errors, 'success' => $success, 'statusLabels' => self::STATUSES,
            'myRequests' => user() && !isAdmin() ? $this->requests->forUser((int) user()['id']) : [],
            'allRequests' => isAdmin() ? $this->requests->all() : [],
        ];
    }
}
