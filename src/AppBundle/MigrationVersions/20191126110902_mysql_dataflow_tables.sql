CREATE TABLE cr_dataflow_job (id INT AUTO_INCREMENT NOT NULL, scheduled_dataflow_id INT DEFAULT NULL, status INT NOT NULL, label VARCHAR(255) NOT NULL, dataflow_type VARCHAR(255) NOT NULL, options LONGTEXT NOT NULL COMMENT '(DC2Type:json)', requested_date DATETIME DEFAULT NULL, count INT DEFAULT NULL, exceptions LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)', start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, INDEX IDX_D0EA3FB85558A221 (scheduled_dataflow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE cr_dataflow_scheduled (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, dataflow_type VARCHAR(255) NOT NULL, options LONGTEXT NOT NULL COMMENT '(DC2Type:json)', frequency VARCHAR(255) NOT NULL, next DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
ALTER TABLE cr_dataflow_job ADD CONSTRAINT FK_D0EA3FB85558A221 FOREIGN KEY (scheduled_dataflow_id) REFERENCES cr_dataflow_scheduled (id);