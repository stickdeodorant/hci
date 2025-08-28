ALTER TABLE leads 
ADD COLUMN trusted_form_cert_id VARCHAR(255) NULL AFTER api_response,
ADD COLUMN convoso_lead_id VARCHAR(100) NULL AFTER trusted_form_cert_id,
ADD INDEX idx_trusted_form (trusted_form_cert_id),
ADD INDEX idx_convoso (convoso_lead_id);