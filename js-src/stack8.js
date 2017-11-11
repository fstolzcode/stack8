/**
 * @fileoverview Implements a VM for the conceptual Stack8 CPU
 * @author Florian Stolz
 * @version 0.1.0
*/

/**
 * Represents the Stack8 CPU
 */
class CPU
{
    constructor()
    {
        ;
    }

    step()
    {
        ;
    }

    run()
    {
        ;
    }
}
/**
 * Represents the internal ALU of the CPU
 */
class ALU
{
    constructor(stackObject)
    {
        this.internalStack = stackObject; 
        this.aluInReg1 = 0;
        this.aluInReg2 = 0;
        this.aluOutReg = 0;
    }

    performAdd(inval1,inval2)
    {
        return ((inval1 + inval2)%256);
    }

    performNand(inval1,inval2)
    {
        return ~(inval1 & inval2);
    }

    performLfsh(inval1,inval2)
    {
        var tempReg = inval1;
        for(var i = 0; i < inval2; i++)
        {
            tempReg = tempReg << 1;
            tempReg = tempReg | (tempReg >> 8);
        }
        tempReg = tempReg & 0xFFFF;
        return tempReg;
    }

    performOperation(opcode)
    {
        if(!(opcode > -1 && opcode < 8))
        {
            return;
        }
        this.aluInReg1 = this.internalStack.pop();
        this.aluInReg2 = this.internalStack.pop();

        if(opcode == 1)
        {
            this.aluOutReg = this.performAdd(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
        }
        if(opcode == 2)
        {
            this.aluOutReg = this.performNand(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
        }
        if(opcode == 3)
        {
            this.aluOutReg = this.performLfsh(this.aluInReg1,this.aluInReg2);
            this.internalStack.push(this.aluOutReg);
        }
    }
}

/**
 * Represents the external memory with a size of 8 Kib
 */
class Memory
{
    constructor()
    {
        this.memArr = new Uint8Array(8192);
    }

    fetch(address)
    {
        return this.memArr[address];
    }

    store(address, value)
    {
        this.memArr[address] = value;
    }

    get memArr()
    {
        return this.memArr;
    }
}

/**
 * Represents an internal Stack with a depth of 8
 */
class Stack
{
    constructor()
    {
        this.stackObject = new Int8Array();
        this.currentLength = 0;
    }

    push(newEntry)
    {
        this.stackObject.copyWithin(1,0);
        this.stackObject[0] = newEntry;
        if(this.currentLength != 8)
        {
            this.currentLength++; 
        }
    }

    pop()
    {
        if(this.currentLength == 0) return 0;

        var poppedValue = this.stackObject[0];

        this.stackObject.copyWithin(0,1);

        this.stackObject[this.currentLength - 1] = 0;
        this.currentLength--;

        return poppedValue;
    }

    inspect()
    {
        console.log("Inspecting Stack:");
        for(var i = 0; i < this.stackObject.length; i++)
        {
            console.log(this.stackObject[i]);
        }
    }

}