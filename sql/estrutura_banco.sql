-- Tabela de serviços
CREATE TABLE servicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_servico VARCHAR(255),
  rua VARCHAR(255),
  bairro VARCHAR(100),
  cidade VARCHAR(100),
  estado VARCHAR(100),
  tipo VARCHAR(100),
  descricao TEXT,
  horario_inicio TIME,
  horario_fim TIME,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8),
  agendamento_pt TEXT,
  agendamento_es TEXT,
  agendamento_en TEXT
);

-- Tabela de categorias fixas (ex: Saúde, Cultura, Assistência)
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) UNIQUE,
  cor VARCHAR(10)
);

INSERT INTO categorias (nome, cor) VALUES
('Saúde', '#28a745'),
('Cultura', '#007bff'),
('Assistência', '#ffc107'),
('Educação', '#6610f2'),
('Esporte', '#fd7e14');

-- Relacionamento N:N
CREATE TABLE servico_categoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  servico_id INT,
  categoria_id INT,
  FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

-- Cache geocodificado
CREATE TABLE geocache (
  endereco VARCHAR(255) PRIMARY KEY,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8)
);
