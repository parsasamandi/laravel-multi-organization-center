/*
 * MIT License
 *
 * Copyright (c) 2018-present Marx J. Moura
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

const sizeUnit = /B|KB|MB|GB/i
const exponentOf = { B: 0, KB: 1, MB: 2, GB: 3 }

class FileSizeWrapper {
  constructor (file) {
    this._file = file
  }

  calculate (unit) {
    unit = sizeUnit.test(unit) ? unit.toUpperCase() : 'B'

    return Number(this._file.size) / Math.pow(1024, exponentOf[unit])
  }

  greaterThan (size, unit) {
    return this.calculate(unit) > Number(size)
  }

  greaterOrEqual (size, unit) {
    return this.calculate(unit) >= Number(size)
  }

  lowerThan (size, unit) {
    return this.calculate(unit) < Number(size)
  }

  lowerOrEqual (size, unit) {
    return this.calculate(unit) <= Number(size)
  }
}

export default FileSizeWrapper
