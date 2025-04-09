CREATE TABLE servicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_servico VARCHAR(255),
  endereco VARCHAR(255),
  bairro VARCHAR(100),
  tipo VARCHAR(100),
  descricao TEXT,
  horario_inicio TIME,
  horario_fim TIME,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8)
);

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) UNIQUE
);

CREATE TABLE servico_categoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  servico_id INT,
  categoria_id INT,
  FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

CREATE TABLE geocache (
  endereco VARCHAR(255) PRIMARY KEY,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8)
);
