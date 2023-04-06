<?php

namespace CT275\Labs;

class khach_hang{
	private $db;

	public $id = -1;
	public $email;
    public $mat_khau;
    public $so_dien_thoai;
    public $dia_chi;
	private $errors = [];

	public function getId()
	{
		return $this->id;
	}

	public function __construct($pdo)
	{
		$this->db = $pdo;
	}

	public function fill(array $data)
	{
		if (isset($data['email'])) {
			$this->email = trim($data['email']);
		}

		if (isset($data['mat_khau'])) {
			$this->mat_khau = trim($data['mat_khau']);
		}
		return $this;
	}

	public function getValidationErrors()
	{
		return $this->errors;
	}

	public function validate()
	{
		if (!$this->email) {
			$this->errors['email'] = 'Email không được rỗng.';
		}
        if (!$this->mat_khau) {
			$this->errors['mat_khau'] = 'Mật khẩu không được rỗng.';
		}

		return empty($this->errors);
	}

	public function all()
	{
		$khach_hangs = [];
		$stmt = $this->db->prepare('select * from khach_hang');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$khach_hang = new khach_hang($this->db);
			$khach_hang->fillFromDB($row);
			$khach_hangs[] = $khach_hang;
		}
		return $khach_hangs;
	}
	
	protected function fillFromDB(array $row) // truyen doi tuong tu db
	{
		[
			'id' => $this->id,
			'email' => $this->email,
            'mat_khau' => $this->mat_khau,
		] = $row;
		return $this;
	}

	public function save()
	{
		$result = false;
		if ($this->id >= 0) {
			$stmt = $this->db->prepare('update khach_hang set email = :email,
mat_khau = :mat_khau where id = :id');
			$result = $stmt->execute([
				'name' => $this->name,
				'phone' => $this->phone,
				'notes' => $this->notes,
				'id' => $this->id
			]);
		} else {
			$stmt = $this->db->prepare(
				'insert into khach_hang (email, mat_khau) values (:email, :mat_khau)'
			);
			$result = $stmt->execute([
				'email' => $this->email,
				'mat_khau' => $this->mat_khau
			]);
			if ($result) {
				$this->id = $this->db->lastInsertId();// lay id giao dich cuoi cung
			}
		}
		return $result;
	}
	public function find($id)
	{
		$stmt = $this->db->prepare('select * from khach_hang where id = :id');
		$stmt->execute(['id' => $id]);
		if ($row = $stmt->fetch()) {
			$this->fillFromDB($row);
			return $this;
		}
		return null;
	}
	public function update(array $data)
	{
		$this->fill($data);
		if ($this->validate()) {
			return $this->save();
		}
		return false;
	}
	public function delete()
	{
		$stmt = $this->db->prepare('delete from khach_hang where id = :id');
		return $stmt->execute(['id' => $this->id]);
	}
}
