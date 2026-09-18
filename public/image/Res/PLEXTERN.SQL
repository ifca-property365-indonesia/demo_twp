if exists (select * from sysobjects where id = object_id('mgr.xpl_post_extern') and sysstat & 0xf = 4)
	drop procedure mgr.xpl_post_extern
GO

/* Procedure Name	: xpl_post_extern		   			*/
/* Function			: Posting Of PL External File 	*/
/* Parameters		: Entity Code, Batch Range			*/
/* Author 			: Ian Jeremy Jones					*/
/*	Date				: 25th February 1997					*/

CREATE PROCEDURE mgr.xpl_post_extern
	@rt_entity_cd 	   char(2),
	@rt_project_no	   char(8),
   @rt_module			char(2),
	@rt_cost_cd			char(8),
	@rt_seq_no			numeric(12,0),
	@rt_line_no			numeric(12,0)
AS
/******* Declaring All Variables *******/
/* Declaring fetch status variables    */
DECLARE @f_extern_fetch		numeric (12,0)

/* Declaring External Variables			*/
DECLARE @f_project_no		char (8)
DECLARE @f_contract_no		char (8)
DECLARE @f_entity_cd			char (2)
DECLARE @f_cost_cd			char (8)
DECLARE @f_year				numeric (12,0)
DECLARE @f_period				numeric (12,0)
DECLARE @f_trx_date			datetime
DECLARE @f_trx_descs			varchar (60)
DECLARE @f_trx_amt			numeric (21,2)
DECLARE @f_trx_mode			char (1)
DECLARE @f_ref_no				char (15)
DECLARE @f_batch_no			numeric (12,0)
DECLARE @f_doc_no				char (10)
DECLARE @f_doc_date			datetime
DECLARE @f_module				char (2)
DECLARE @f_fin_year			numeric (12,0)
DECLARE @f_fin_month			numeric (12,0)
DECLARE @f_audit_user		char (10)
DECLARE @f_audit_date		datetime
DECLARE @f_sequence_no		numeric (12,0)
DECLARE @f_line_no			numeric (12,0)
DECLARE @f_lot_no				char (8)

DECLARE extern_cursor CURSOR FOR
  SELECT  project_no,       contract_no,   
          entity_cd,        cost_cd,   
          year,             period,   
          trx_date,         trx_descs,   
          trx_amt,          trx_mode,   
          ref_no,           batch_no,
          doc_no,           doc_date,   
          module,           fin_year,   
          fin_month,        audit_user,   
          audit_date,       sequence_no,   
          line_no,			 lot_no
  FROM    mgr.pl_extern  
  WHERE   entity_cd 		= @rt_entity_cd
  AND     project_no		= @rt_project_no
  AND     module    		= @rt_module
  AND		 cost_cd   		= @rt_cost_cd
  AND		 sequence_no	= @rt_seq_no
  AND		 line_no			= @rt_line_no

 OPEN extern_cursor

 SELECT @f_extern_fetch = 0

 /* Start Of External While Loop */
 WHILE (@f_extern_fetch <> -1)
 BEGIN
   FETCH NEXT FROM extern_cursor
	 INTO  @f_project_no,       @f_contract_no,   
          @f_entity_cd,        @f_cost_cd,   
          @f_year,             @f_period,   
          @f_trx_date,         @f_trx_descs,   
          @f_trx_amt,          @f_trx_mode,   
          @f_ref_no,           @f_batch_no,
          @f_doc_no,           @f_doc_date,   
          @f_module,           @f_fin_year,   
          @f_fin_month,        @f_audit_user,   
          @f_audit_date,       @f_sequence_no,   
          @f_line_no,			 @f_lot_no

	SELECT @f_extern_fetch = @@FETCH_STATUS
	IF @f_extern_fetch <> -1
   BEGIN
     EXEC mgr.x_post_plldg  @rt_entity_cd,	 	@f_project_no,		
	         			       @f_cost_cd,		   @f_trx_date,		
  								    @f_trx_descs,	 	   @f_trx_amt,		
        							 @f_trx_mode,	  	   @f_ref_no,		
  									 @f_batch_no,	  	   @f_doc_no,
                            @f_doc_date,		   @rt_module,	
                            @f_fin_year,		   @f_fin_month,
            	       		 @f_audit_user,	   @f_audit_date,	   	
                            @f_sequence_no,		1,
									 @f_line_no,			@f_contract_no,
									 @f_lot_no
     IF @@ERROR <> 0 
     BEGIN
       ROLLBACK TRANSACTION
       DEALLOCATE extern_cursor
	    RAISERROR('Could Not Post Project Ledger File (pl_extern) File.',16,-1)
		 RAISERROR('',16,-1)
       RETURN
     END

	  DELETE mgr.pl_extern  
 	  WHERE   entity_cd 	= @rt_entity_cd
     AND     project_no	= @rt_project_no
     AND     module    	= @rt_module
     AND		cost_cd   	= @rt_cost_cd
     AND		sequence_no	= @rt_seq_no
     AND		line_no		= @rt_line_no

     IF @@ERROR <> 0 
     BEGIN
       ROLLBACK TRANSACTION
	    DEALLOCATE extern_cursor
	    RAISERROR('Could Not Delete Project External File (pl_extern) File.',16,-1)
       RETURN
     END
   END
 END
 /* End Of External While Loop */
 DEALLOCATE extern_cursor

RETURN
/* End Of Procedure */
GO

GRANT ALL ON mgr.xpl_post_extern TO pbteam
GO