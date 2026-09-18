/* Trigger Name	: t_ins_pl_ledger			        */
/* Function			: Insert Trigger For pl_ledger  */
/* Author 			: Ian Jeremy Jones			     */
/*	Date				: 03rd October 1995			     */

/* GRANT ALL ON mgr.t_ins_pl_ledger TO pbteam */
/* DROP TRIGGER mgr.t_ins_pl_ledger           */

CREATE TRIGGER mgr.t_ins_pl_ledger
ON mgr.pl_ledger
FOR INSERT
AS
BEGIN
  DECLARE @f_dummy CHAR (2)

  /*******************************************************/
  /****************** Updating Summary *******************/
  /*******************************************************/

  SELECT @f_dummy = mgr.pl_budget.entity_cd
  FROM   mgr.pl_budget,		inserted
  WHERE  mgr.pl_budget.entity_cd   = inserted.entity_cd
  AND    mgr.pl_budget.project_no  = inserted.project_no
  AND    mgr.pl_budget.cost_cd     = inserted.cost_cd
  IF (@@ROWCOUNT > 0 )
  BEGIN
    UPDATE mgr.pl_budget
    SET    mgr.pl_budget.todate_amt  =
			    CASE 
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_budget.todate_amt + inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_budget.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_budget.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_budget.todate_amt + inserted.trx_amt
			    END
	 FROM   mgr.pl_budget,  		  inserted
    WHERE  mgr.pl_budget.entity_cd   = inserted.entity_cd
    AND    mgr.pl_budget.project_no  = inserted.project_no
    AND    mgr.pl_budget.cost_cd     = inserted.cost_cd
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Update Project Budget (pl_budget) File.',16,-1)
      RETURN
    END
  END
  ELSE
  BEGIN
    INSERT INTO mgr.pl_budget  (
             mgr.pl_budget.entity_cd,      mgr.pl_budget.project_no,   
             mgr.pl_budget.cost_cd,        mgr.pl_budget.trx_mode,   
             mgr.pl_budget.base_budget,    mgr.pl_budget.revisions,   
             mgr.pl_budget.revise_budget,  mgr.pl_budget.todate_amt,   
             mgr.pl_budget.commit_amt,     mgr.pl_budget.audit_user,   
             mgr.pl_budget.audit_date,		 mgr.pl_budget.forecast_amt  )
		SELECT inserted.entity_cd,				inserted.project_no,		
				 inserted.cost_cd,			
             CASE
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R')
                          THEN 'C'
					ELSE 'D'
				 END,
				 0,  			  						 0,
   			 0,
				 CASE
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN inserted.trx_amt
			    END,
             0,	
				 inserted.audit_user,		inserted.audit_date, 	0
		FROM inserted
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Insert Project Budget (pl_budget) File.',16,-1)
      RETURN
    END
  END

  SELECT @f_dummy = mgr.pl_budget_sch.entity_cd
  FROM   mgr.pl_budget_sch,		inserted
  WHERE  mgr.pl_budget_sch.entity_cd   = inserted.entity_cd
  AND    mgr.pl_budget_sch.project_no  = inserted.project_no
  AND    mgr.pl_budget_sch.cost_cd     = inserted.cost_cd
  AND    mgr.pl_budget_sch.year        = inserted.year
  AND    mgr.pl_budget_sch.period      = inserted.period
  IF (@@ROWCOUNT > 0 )
  BEGIN
    UPDATE mgr.pl_budget_sch  
    SET    mgr.pl_budget_sch.todate_amt  =
			    CASE 
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_budget_sch.todate_amt + inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_budget_sch.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_budget_sch.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_budget_sch.todate_amt + inserted.trx_amt
			    END
	 FROM   mgr.pl_budget_sch,		inserted
    WHERE  mgr.pl_budget_sch.entity_cd   = inserted.entity_cd
    AND    mgr.pl_budget_sch.project_no  = inserted.project_no
    AND    mgr.pl_budget_sch.cost_cd     = inserted.cost_cd
    AND    mgr.pl_budget_sch.year        = inserted.year
    AND    mgr.pl_budget_sch.period      = inserted.period
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Update Project Budget Schedule (pl_budget_sch) File.',16,-1)
      RETURN
    END
  END
  ELSE
  BEGIN	
	 INSERT INTO mgr.pl_budget_sch (
         mgr.pl_budget_sch.entity_cd,    mgr.pl_budget_sch.project_no,   
         mgr.pl_budget_sch.cost_cd,      mgr.pl_budget_sch.year,   
         mgr.pl_budget_sch.period,       mgr.pl_budget_sch.base_budget,   
         mgr.pl_budget_sch.revisions,    mgr.pl_budget_sch.revise_budget,   
         mgr.pl_budget_sch.todate_amt,   mgr.pl_budget_sch.revise_foreign,   
         mgr.pl_budget_sch.audit_user,   mgr.pl_budget_sch.audit_date,
			mgr.pl_budget_sch.forecast_amt  )
		SELECT inserted.entity_cd,		     inserted.project_no,		
             inserted.cost_cd,	        inserted.year,			   
				 inserted.period,				  0,		
				 0,                          0,	
				 CASE
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.entity_cd  = inserted.entity_cd
							  AND   mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN inserted.trx_amt
			    END,
				 0,				 				inserted.audit_user,		
 				 inserted.audit_date,		0
		FROM inserted
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Insert Project Budget Schedule (pl_budget_sch) File.',16,-1)
      RETURN
    END 
  END 

  /*******************************************************/
  /*************** Updating Lot By Lot *******************/
  /*******************************************************/
  /*
  SELECT @f_dummy = mgr.pl_lot_budget.entity_cd
  FROM   mgr.pl_lot_budget,		inserted
  WHERE  mgr.pl_lot_budget.entity_cd   = inserted.entity_cd
  AND    mgr.pl_lot_budget.project_no  = inserted.project_no
  AND    mgr.pl_lot_budget.lot_no      = inserted.lot_no
  AND    mgr.pl_lot_budget.cost_cd     = inserted.cost_cd
  IF (@@ROWCOUNT > 0 )
  BEGIN
    UPDATE mgr.pl_lot_budget
    SET    mgr.pl_lot_budget.todate_amt  =
			    CASE 
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_lot_budget.todate_amt + inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_lot_budget.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_lot_budget.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_lot_budget.todate_amt + inserted.trx_amt
			    END
	 FROM   mgr.pl_lot_budget,  		  inserted
    WHERE  mgr.pl_lot_budget.entity_cd   = inserted.entity_cd
    AND    mgr.pl_lot_budget.project_no  = inserted.project_no
    AND    mgr.pl_lot_budget.lot_no      = inserted.lot_no
    AND    mgr.pl_lot_budget.cost_cd     = inserted.cost_cd
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Update Project Lot Budget (pl_lot_budget) File.',16,-1)
      RETURN
    END
  END
  ELSE
  BEGIN
    INSERT INTO mgr.pl_lot_budget  (
             mgr.pl_lot_budget.entity_cd,      mgr.pl_lot_budget.project_no,   
             mgr.pl_lot_budget.cost_cd,        mgr.pl_lot_budget.trx_mode,   
             mgr.pl_lot_budget.base_budget,    mgr.pl_lot_budget.revisions,   
             mgr.pl_lot_budget.revise_budget,  mgr.pl_lot_budget.todate_amt,   
             mgr.pl_lot_budget.commit_amt,     mgr.pl_lot_budget.audit_user,   
             mgr.pl_lot_budget.audit_date,	  mgr.pl_lot_budget.lot_no  )
		SELECT inserted.entity_cd,				inserted.project_no,		
				 inserted.cost_cd,			
             CASE
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R')
                          THEN 'C'
					ELSE 'D'
				 END,
				 0,  			  						 0,
   			 0,
				 CASE
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN inserted.trx_amt
			    END,
             0,	
				 inserted.audit_user,		inserted.audit_date,
				 inserted.lot_no
		FROM inserted
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Insert Project Lot Budget (pl_lot_budget) File.',16,-1)
      RETURN
    END
  END

  SELECT @f_dummy = mgr.pl_lot_budget_sch.entity_cd
  FROM   mgr.pl_lot_budget_sch,		inserted
  WHERE  mgr.pl_lot_budget_sch.entity_cd   = inserted.entity_cd
  AND    mgr.pl_lot_budget_sch.project_no  = inserted.project_no
  AND    mgr.pl_lot_budget_sch.lot_no		 = inserted.lot_no
  AND    mgr.pl_lot_budget_sch.cost_cd     = inserted.cost_cd
  AND    mgr.pl_lot_budget_sch.year        = inserted.year
  AND    mgr.pl_lot_budget_sch.period      = inserted.period
  IF (@@ROWCOUNT > 0 )
  BEGIN
    UPDATE mgr.pl_lot_budget_sch  
    SET    mgr.pl_lot_budget_sch.todate_amt  =
			    CASE 
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_lot_budget_sch.todate_amt + inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_lot_budget_sch.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN mgr.pl_lot_budget_sch.todate_amt - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN mgr.pl_lot_budget_sch.todate_amt + inserted.trx_amt
			    END
	 FROM   mgr.pl_lot_budget_sch,		inserted
    WHERE  mgr.pl_lot_budget_sch.entity_cd   = inserted.entity_cd
    AND    mgr.pl_lot_budget_sch.project_no  = inserted.project_no
    AND    mgr.pl_lot_budget_sch.lot_no      = inserted.lot_no
    AND    mgr.pl_lot_budget_sch.cost_cd     = inserted.cost_cd
    AND    mgr.pl_lot_budget_sch.year        = inserted.year
    AND    mgr.pl_lot_budget_sch.period      = inserted.period
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Update Project Lot Budget Schedule (pl_lot_budget_sch) File.',16,-1)
      RETURN
    END
  END
  ELSE
  BEGIN	
	 INSERT INTO mgr.pl_lot_budget_sch (
         mgr.pl_lot_budget_sch.entity_cd,    mgr.pl_lot_budget_sch.project_no,   
         mgr.pl_lot_budget_sch.cost_cd,      mgr.pl_lot_budget_sch.year,   
         mgr.pl_lot_budget_sch.period,       mgr.pl_lot_budget_sch.base_budget,   
         mgr.pl_lot_budget_sch.revisions,    mgr.pl_lot_budget_sch.revise_budget,   
         mgr.pl_lot_budget_sch.todate_amt,   mgr.pl_lot_budget_sch.revise_foreign,   
         mgr.pl_lot_budget_sch.audit_user,   mgr.pl_lot_budget_sch.audit_date,
			mgr.pl_lot_budget_sch.lot_no  )
		SELECT inserted.entity_cd,		     inserted.project_no,		
             inserted.cost_cd,	        inserted.year,			   
				 inserted.period,				  0,		
				 0,                          0,	
				 CASE
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) = 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'C' ) THEN 0 - inserted.trx_amt
					WHEN ((SELECT mgr.pl_costcode.cost_type 
							  FROM  mgr.pl_costcode
							  WHERE mgr.pl_costcode.project_no = inserted.project_no
							  AND   mgr.pl_costcode.cost_cd    = inserted.cost_cd) <> 'R'
                       AND   inserted.trx_mode          = 'D' ) THEN inserted.trx_amt
			    END,
				 0,				 inserted.audit_user,		
 				 inserted.audit_date,	inserted.lot_no
		FROM inserted
    IF @@ERROR <> 0
    BEGIN
      RAISERROR('Could Not Insert Project Lot Budget Schedule (pl_lot_budget_sch) File.',16,-1)
      RETURN
    END 
  END 
  */

END 
GO

GRANT ALL ON mgr.t_ins_pl_ledger TO pbteam
GO