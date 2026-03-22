<?php
declare(strict_types=1);
namespace App\Services;
final class PDFViewer{
	private readonly string $filePath;
	private readonly string $fileKey;
	private readonly array $allowedFiles;
	public function __construct(string $fileKey, array $allowedFiles){
		$this->fileKey=$fileKey;
		$this->allowedFiles=$allowedFiles;
		$this->validateFile();
		$this->filePath=$this->allowedFiles[$this->fileKey];
	}
	private function validateFile(): void{
		if(!isset($this->allowedFiles[$this->fileKey])){
			throw new \RuntimeException("Arquivo inválido");
		}
		if(!file_exists($this->allowedFiles[$this->fileKey])){
			throw new \RuntimeException("Arquivo não encontrado");
		}
	}
	public function generateToken(): string{
		$token=bin2hex(random_bytes(16));
		$_SESSION['pdf_tokens'][$this->fileKey]=['token'=>$token,'expires'=>time()+600];
		return $token;
	}
	public function verifyToken(string $token): void{
		if(!isset($_SESSION['pdf_tokens'][$this->fileKey])||$_SESSION['pdf_tokens'][$this->fileKey]['token']!==$token){
			throw new \RuntimeException("Acesso negado");
		}
		if($_SESSION['pdf_tokens'][$this->fileKey]['expires']<time()){
			unset($_SESSION['pdf_tokens'][$this->fileKey]);
			throw new \RuntimeException("Token expirado");
		}
	}
	public function getFilePath(): string{
		return $this->filePath;
	}
}