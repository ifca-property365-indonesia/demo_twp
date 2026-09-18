if exists (select * from sysobjects where id = object_id('mgr.x_post_plldg') and sysstat & 0xf = 4)
	drop procedure mgr.x_post_plldg
GO

/* Procedure Name	: x_post_plldg   			   */
/* Function			: Posting Of PL Ledger	   */
/* Author 			: Ian Jeremy Jones			*/
/*	Date				: 03rd October 1995			*/

CREATE PROCEDURE mgr.x_post_plldg
  @rt_entity_cd	char (2),
  @rt_project		char (8),
  @rt_cost_cd		char (8),
  @rt_trx_date		datetime,   
  @rt_trx_descs	varchar (60),
  @rt_trx_amt		numeric (21,2),
  @rt_trx_mode		char (1),
  @rt_ref_no		char (15),
  @rt_batch_no		numeric (12,0),
  @rt_doc_no		char (10),   
  @rt_doc_date		datetime,
  @rt_module		char (2),
  @rt_fin_year		numeric (12,0),
  @rt_fin_month	numeric (12,0),
  @rt_audit_user	char (10),   
  @rt_audit_date	datetime, 
  @rt_sequence		numeric (12,0),
  @rt_link_pl		numeric (12,0),
  @rt_trx_no		numeric (12,0),
  @rt_contract_no	char (8),
  @rt_lot_no		char (8)
AS

DECLARE @f_month 				numeric (12,0)
DECLARE @f_year				numeric (12,0)
DECLARE @f_apport_method	char (1)
DECLARE @f_last_lot			char (8)
DECLARE @f_total_land		numeric (21,2)
DECLARE @f_total_build		numeric (21,2)
DECLARE @f_total_value		numeric (21,2)
DECLARE @f_lot_fetch			numeric (12,0)
DECLARE @f_one_unit			numeric (21,2)
DECLARE @f_build_up_area	numeric (21,2)
DECLARE @f_extra_land_area	numeric (21,2)
DECLARE @f_land_area			numeric (21,2)
DECLARE @f_lot_no				char (8)
DECLARE @f_apport_value		numeric (21,2)
DECLARE @f_total_lot			numeric (12,0)


  SELECT @f_month = DATEPART(month,@rt_doc_date),
			@f_year  = DATEPART(year,@rt_doc_date)

  IF (@rt_link_pl = 1)
  BEGIN
    INSERT INTO mgr.pl_ledger (
      entity_cd,           project_no,   
      cost_cd,             year,   
      period,              trx_date,   
      trx_descs,           trx_amt,   
      trx_mode,            ref_no,   
      batch_no,            doc_no,   
      doc_date,            module,
      fin_year,			   fin_month,
      audit_user,          audit_date,          
      sequence_no,			line_no,
      contract_no,			lot_no,
		status )  VALUES (
  		@rt_entity_cd,	 		@rt_project,		
  		@rt_cost_cd,		 	@f_year,
  		@f_month,			 	@rt_trx_date,		
  		@rt_trx_descs,	  		@rt_trx_amt,		
  		@rt_trx_mode,	  		@rt_ref_no,		
  		@rt_batch_no,	  		@rt_doc_no,		
      @rt_doc_date,  		@rt_module,		  		
      @rt_fin_year,			@rt_fin_month,
      @rt_audit_user,		@rt_audit_date,	  	
      @rt_sequence,			@rt_trx_no,
      @rt_contract_no,		@rt_lot_no,
		'A' )
    IF @@ERROR<>0
    BEGIN
      RAISERROR('Could Not Insert Project Ledger (pl_ledger) File.',16,-1)
      RETURN
    END
  END
  ELSE
  BEGIN
    INSERT INTO mgr.pl_extern  (
      project_no,        	entity_cd,   
      cost_cd,             year,   
      period,              trx_date,   
      trx_descs,           trx_amt,   
      trx_mode,            ref_no,   
      batch_no,            doc_no,   
      doc_date,			   module,
      fin_year,            fin_month,
      audit_user,          audit_date, 
      sequence_no,			line_no,
      contract_no,			lot_no )  VALUES (
      @rt_project, 	 		@rt_entity_cd,		
  		@rt_cost_cd,		 	@f_year,
  		@f_month,			 	@rt_trx_date,		
  		@rt_trx_descs,	  		@rt_trx_amt,		
  		@rt_trx_mode,	  		@rt_ref_no,		
  		@rt_batch_no,	  		@rt_doc_no,		
      @rt_doc_date,  		@rt_module,	
	  	@rt_fin_year,			@rt_fin_month,
      @rt_audit_user,		@rt_audit_date,	 
   	@rt_sequence,			@rt_trx_no,
      @rt_contract_no,		@rt_lot_no )  
    IF @@ERROR<>0
    BEGIN
      RAISERROR('Could Not Insert Project External (pl_extern) File.',16,-1)
      RETURN
    END
  END
RETURN
GO

GRANT ALL ON mgr.x_post_plldg  TO pbteam
GO

