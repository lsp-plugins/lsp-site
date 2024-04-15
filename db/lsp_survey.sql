CREATE DATABASE lsp_survey DEFAULT CHARSET=utf8;

CREATE TABLE lsp_market_survey
(
  nID bigint(20) NOT NULL AUTO_INCREMENT,
  dtDateTime timestamp NOT NULL DEFAULT current_timestamp(),
  user_agent varchar(512) DEFAULT NULL,
  status varchar(8) NOT NULL,
  income varchar(8) NOT NULL,
  purchase varchar(8) NOT NULL,
  familiar varchar(8) NOT NULL,
  para_eq int(11) NOT NULL,
  mb_comp int(11) NOT NULL,
  flanger int(11) NOT NULL,
  pd_delay int(11) NOT NULL,
  
  PRIMARY KEY (nID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE lsp_market_survey_roles
(
  nSurveyID bigint(20) NOT NULL,
  value varchar(8) NOT NULL,
  
  CONSTRAINT FK_SURVEY_ID FOREIGN KEY (nSurveyID) REFERENCES lsp_market_survey(nID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
